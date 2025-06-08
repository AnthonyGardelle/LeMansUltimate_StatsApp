<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\LmuSession;

class ProcessXmlFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected array $infos;

    public function __construct(string $filePath, array $infos)
    {
        $this->filePath = $filePath;
        $this->infos = $infos;
    }

    public function handle(): void
    {
        try {
            $xml = $this->loadXml();
            if (!$xml)
                return;

            $sessionType = $this->getSessionType($xml);
            $lmuSessionType = $this->getOrCreateLmuSessionType($sessionType);
            $track = $this->getOrCreateTrack($xml);
            $lmuSession = $this->getOrCreateLmuSession($xml, $sessionType, $lmuSessionType, $track);
            $this->processDrivers($xml, $sessionType, $lmuSession);

            Cache::increment('upload_progress_' . $this->infos['user_id']);
        } catch (\Throwable $e) {
            Log::error("Error processing XML file: " . $e->getMessage(), ['filePath' => $this->filePath]);
        }
    }

    protected function loadXml(): ?\SimpleXMLElement
    {
        $path = storage_path("app/public/{$this->filePath}");
        $content = file_get_contents($path);

        if ($content === false) {
            Log::error("Impossible de lire le fichier XML à l'emplacement : {$path}");
            return null;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            Log::error("Échec du parsing XML", [
                'filePath' => $this->filePath,
                'errors' => array_map(fn($e) => $e->message, $errors)
            ]);
            Cache::decrement('upload_total_' . $this->infos['user_id']);
            return null;
        }

        return $xml;
    }

    protected function getSessionType(\SimpleXMLElement $xml): ?string
    {
        foreach (['Practice1', 'Qualify', 'Race'] as $type) {
            if (isset($xml->RaceResults->{$type})) {
                return $type;
            }
        }
        return null;
    }

    protected function getOrCreateLmuSessionType(string $type)
    {
        $service = app(\App\Services\LmuSessionTypeService::class);
        return $service->getLmuSessionTypeByName($type)
            ?? $service->createLmuSessionType(['name' => $type]);
    }

    protected function getOrCreateTrack(\SimpleXMLElement $xml)
    {
        $service = app(\App\Services\TrackService::class);
        $data = [
            'track_venue' => (string) $xml->RaceResults->TrackVenue,
            'track_course' => (string) $xml->RaceResults->TrackCourse,
            'track_event' => (string) $xml->RaceResults->TrackEvent,
            'track_length' => (float) $xml->RaceResults->TrackLength,
        ];

        return $service->getTrack(...array_values($data)) ?? $service->createTrack($data);
    }

    protected function getOrCreateLmuSession($xml, string $sessionType, $sessionTypeModel, $track)
    {
        $service = app(\App\Services\LmuSessionService::class);

        $details = [
            'lmu_session_type_id' => $sessionTypeModel->id,
            'track_id' => $track->id,
            'starting_at' => Carbon::createFromFormat('Y/m/d H:i:s', (string) $xml->RaceResults->{$sessionType}->TimeString),
            'duration' => (int) $xml->RaceResults->{$sessionType}->Minutes,
            'mech_fail_rate' => (int) $xml->RaceResults->MechFailRate,
            'damage_multiplier' => (int) $xml->RaceResults->DamageMult,
            'fuel_multiplier' => (int) $xml->RaceResults->FuelMult,
            'tire_multiplier' => (int) $xml->RaceResults->TireMult,
            'parc_ferme' => (int) $xml->RaceResults->ParcFerme,
            'fixed_setups' => (int) $xml->RaceResults->FixedSetups,
            'free_settings' => (int) $xml->RaceResults->FreeSettings,
            'fixed_upgrades' => (int) $xml->RaceResults->FixedUpgrades,
            'limited_tires' => (bool) $xml->RaceResults->LimitedTires,
            'tire_warmers' => (bool) $xml->RaceResults->TireWarmers,
        ];

        return $service->getLmuSession($details) ?? $service->createLmuSession($details);
    }
    protected function processDrivers(\SimpleXMLElement $xml, string $sessionType, LmuSession $lmuSession): void
    {
        $carTypeService = app(\App\Services\CarTypeService::class);
        $carClassService = app(\App\Services\CarClassService::class);
        $teamService = app(\App\Services\TeamService::class);
        $carService = app(\App\Services\CarService::class);
        $driverService = app(\App\Services\DriverService::class);
        $lmuSessionParticipationService = app(\App\Services\LmuSessionParticipationService::class);
        $lmuRaceSessionParticipationService = app(\App\Services\LmuRaceSessionParticipationService::class);

        $carTypes = $carClasses = $teams = $carsNumbers = $driversList = [];

        foreach ($xml->RaceResults->{$sessionType}->Driver as $driver) {
            $carTypeName = (string) $driver->CarType;
            $carClassName = (string) $driver->CarClass;
            $teamName = (string) $driver->TeamName;
            $carNumber = (string) $driver->CarNumber;
            $driverFullName = (string) $driver->Name;

            $carTypes[$carTypeName] ??= $carTypeService->getCarTypeByName($carTypeName)
                ?? $carTypeService->createCarType(['car_type_name' => $carTypeName]);

            $carClasses[$carClassName] ??= $carClassService->getCarClassByName($carClassName)
                ?? $carClassService->createCarClass(['car_class_name' => $carClassName]);

            $teams[$teamName] ??= $teamService->getTeamByName($teamName)
                ?? $teamService->createTeam(['team_name' => $teamName]);

            $carsNumbers[$carNumber] ??= $carService->getCarByCarNumber($carNumber)
                ?? $carService->createCar([
                    'car_number' => $carNumber,
                    'car_type_id' => $carTypes[$carTypeName]->id,
                    'car_class_id' => $carClasses[$carClassName]->id,
                    'team_id' => $teams[$teamName]->id,
                ]);

            $driversList[$driverFullName] ??= $driverService->getDriverByFullName($driverFullName)
                ?? $driverService->createDriver([
                    'full_name' => $driverFullName,
                    'is_player' => $driver->isPlayer,
                ]);

            $lmuSessionData = [
                'lmu_session_id' => $lmuSession->id,
                'driver_id' => $driversList[$driverFullName]->id,
                'car_id' => $carsNumbers[$carNumber]->id,
                'finish_position' => (int) $driver->Position,
                'class_finish_position' => (int) $driver->ClassPosition,
                'laps_completed' => (int) $driver->Laps,
                'pit_stops_executed' => (int) $driver->Pitstops,
                'best_lap_time' => (float) $driver->BestLapTime,
                'finish_status' => (string) $driver->FinishStatus,
                'dnf_reason' => (string) $driver->DNFReason,
            ];

            $existingParticipation = $lmuSessionParticipationService->getLmuSessionParticipation($lmuSessionData);

            if ($existingParticipation) {
                $lmuSessionParticipation = $existingParticipation;
            } else {
                $newParticipation = $lmuSessionParticipationService->createLmuSessionParticipation($lmuSessionData);
                $lmuSessionParticipation = $newParticipation;
            }

            if ($sessionType === 'Race') {
                $lmuRaceSessionData = [
                    'lmu_session_participation_id' => $lmuSessionParticipation->id,
                    'grid_position' => (int) $driver->GridPos,
                    'class_grid_position' => (int) $driver->ClassGridPos,
                    'finish_time' => (float) $driver->FinishTime,
                ];

                $existingRaceParticipation = $lmuRaceSessionParticipationService->getLmuRaceSessionParticipation($lmuRaceSessionData);
                if (!$existingRaceParticipation) {
                    $lmuRaceSessionParticipationService->createLmuRaceSessionParticipation($lmuRaceSessionData);
                }
            }

            // Process laps for this driver
            $this->processDriverLaps($driver, $lmuSessionParticipation);
        }
    }

    /**
     * Process laps for a specific driver
     */
    protected function processDriverLaps(\SimpleXMLElement $driver, $lmuSessionParticipation): void
    {
        $lmuLapService = app(\App\Services\LmuLapService::class);

        if (!isset($driver->Lap)) {
            return; // No laps data for this driver
        }

        foreach ($driver->Lap as $lap) {
            $lapData = [
                'lmu_session_participation_id' => $lmuSessionParticipation->id,
                'lap_number' => (int) $lap['num'],
                'finish_position' => (int) $lap['p'],
                'lap_time' => (float) $lap,
                'top_speed' => (float) $lap['topspeed'],
                'remaining_fuel' => (float) $lap['fuel'],
                'fuel_used' => (float) $lap['fuelUsed'],
                'remaining_virtual_energy' => 0.0,
                'virtual_energy_used' => 0.0,
                'tire_wear_fl' => (float) $lap['twfl'],
                'tire_wear_fr' => (float) $lap['twfr'],
                'tire_wear_rl' => (float) $lap['twrl'],
                'tire_wear_rr' => (float) $lap['twrr'],
            ];

            Log::info("Processing lap", [
                'lap_number' => $lapData['lap_number'],
                'driver_participation_id' => $lmuSessionParticipation->id,
                'lap_time' => $lapData['lap_time']
            ]);

            $existingLap = $lmuLapService->getLmuLap($lapData);
            if (!$existingLap) {
                try {
                    $lmuLapService->createLmuLap($lapData);
                    Log::info("Created new lap", [
                        'lap_number' => $lapData['lap_number'],
                        'driver_participation_id' => $lmuSessionParticipation->id
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to create lap", [
                        'error' => $e->getMessage(),
                        'lap_data' => $lapData
                    ]);
                }
            } else {
                Log::info("Lap already exists", [
                    'lap_number' => $lapData['lap_number'],
                    'existing_lap_id' => $existingLap->id
                ]);
            }
        }
    }
}