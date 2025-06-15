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
            if (!$xml) {
                return;
            }

            $sessionType = $this->getSessionType($xml);
            if (!$sessionType) {
                Log::warning("No valid session type found in XML file", ['filePath' => $this->filePath]);
                return;
            }

            $lmuSessionType = $this->getOrCreateLmuSessionType($sessionType);
            $track = $this->getOrCreateTrack($xml);
            $lmuSession = $this->getOrCreateLmuSession($xml, $sessionType, $lmuSessionType, $track);

            $this->processDrivers($xml, $sessionType, $lmuSession);

            Cache::increment('upload_progress_' . $this->infos['user_id']);
        } catch (\Throwable $e) {
            Log::error("Error processing XML file: " . $e->getMessage(), [
                'filePath' => $this->filePath,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function loadXml(): ?\SimpleXMLElement
    {
        $path = storage_path("app/public/{$this->filePath}");

        if (!file_exists($path)) {
            Log::error("XML file not found", ['path' => $path]);
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            Log::error("Failed to read XML file", ['path' => $path]);
            return null;
        }

        return $this->parseXmlContent($content);
    }

    protected function parseXmlContent(string $content): ?\SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            Log::error("XML parsing failed", [
                'filePath' => $this->filePath,
                'errors' => array_map(fn($e) => trim($e->message), $errors)
            ]);
            Cache::decrement('upload_total_' . $this->infos['user_id']);
            return null;
        }

        return $xml;
    }

    protected function getSessionType(\SimpleXMLElement $xml): ?string
    {
        $sessionTypes = ['Practice1', 'Qualify', 'Race'];

        foreach ($sessionTypes as $type) {
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
        $trackData = $this->extractTrackData($xml);

        return $service->getTrack(...array_values($trackData))
            ?? $service->createTrack($trackData);
    }

    protected function extractTrackData(\SimpleXMLElement $xml): array
    {
        return [
            'track_venue' => (string) $xml->RaceResults->TrackVenue,
            'track_course' => (string) $xml->RaceResults->TrackCourse,
            'track_event' => (string) $xml->RaceResults->TrackEvent,
            'track_length' => (float) $xml->RaceResults->TrackLength,
        ];
    }

    protected function getOrCreateLmuSession($xml, string $sessionType, $sessionTypeModel, $track)
    {
        $service = app(\App\Services\LmuSessionService::class);
        $sessionData = $this->extractSessionData($xml, $sessionType, $sessionTypeModel, $track);

        if ($service->getLmuSession($sessionData)) {
            Cache::increment('upload_progress_' . $this->infos['user_id']);
            return null;
        }
        return $service->createLmuSession($sessionData);
    }

    protected function extractSessionData($xml, string $sessionType, $sessionTypeModel, $track): array
    {
        $lmuSessionGroupService = app(\App\Services\LmuSessionGroupService::class);

        $lmuSessionGroupData = [
            'starting_at' => $xml->RaceResults->DateTime,
            'hashcode' => hash('sha256', implode('|', [
                $sessionTypeModel->id,
                $track->id,
                $xml->RaceResults->DateTime,
                (int) $xml->RaceResults->{$sessionType}->Minutes,
                (int) $xml->RaceResults->MechFailRate,
                (int) $xml->RaceResults->DamageMult,
                (int) $xml->RaceResults->FuelMult,
                (int) $xml->RaceResults->TireMult,
                (int) $xml->RaceResults->ParcFerme,
                (int) $xml->RaceResults->FixedSetups,
                (int) $xml->RaceResults->FreeSettings,
                (int) $xml->RaceResults->FixedUpgrades,
                (bool) $xml->RaceResults->LimitedTires,
                (bool) $xml->RaceResults->TireWarmers,
            ])),
        ];

        $lmuSessionGroup = $lmuSessionGroupService->getLmuSessionGroup($lmuSessionGroupData)
            ?? $lmuSessionGroupService->createLmuSessionGroup($lmuSessionGroupData);

        return [
            'lmu_session_type_id' => $sessionTypeModel->id,
            'track_id' => $track->id,
            'lmu_session_group_id' => $lmuSessionGroup->id,
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
    }

    protected function processDrivers(\SimpleXMLElement $xml, string $sessionType, LmuSession $lmuSession): void
    {
        $services = $this->initializeServices();
        $entityCaches = $this->initializeEntityCaches();

        foreach ($xml->RaceResults->{$sessionType}->Driver as $driver) {
            try {
                $entities = $this->getOrCreateDriverEntities($driver, $services, $entityCaches);
                $lmuSessionParticipation = $this->createOrGetSessionParticipation($driver, $lmuSession, $entities, $services);

                if ($sessionType === 'Race') {
                    $this->createRaceParticipation($driver, $lmuSessionParticipation, $services);
                }

                $this->processDriverLaps($driver, $lmuSessionParticipation);
            } catch (\Exception $e) {
                Log::error("Error processing driver data", [
                    'driver' => (string) $driver->Name,
                    'error' => $e->getMessage(),
                    'filePath' => $this->filePath
                ]);
            }
        }
    }

    protected function initializeServices(): array
    {
        return [
            'carType' => app(\App\Services\CarTypeService::class),
            'carClass' => app(\App\Services\CarClassService::class),
            'team' => app(\App\Services\TeamService::class),
            'car' => app(\App\Services\CarService::class),
            'driver' => app(\App\Services\DriverService::class),
            'sessionParticipation' => app(\App\Services\LmuSessionParticipationService::class),
            'raceParticipation' => app(\App\Services\LmuRaceSessionParticipationService::class),
        ];
    }

    protected function initializeEntityCaches(): array
    {
        return [
            'carTypes' => [],
            'carClasses' => [],
            'teams' => [],
            'cars' => [],
            'drivers' => []
        ];
    }

    protected function getOrCreateDriverEntities(\SimpleXMLElement $driver, array $services, array &$entityCaches): array
    {
        $carTypeName = (string) $driver->CarType;
        $carClassName = (string) $driver->CarClass;
        $teamName = (string) $driver->TeamName;
        $carNumber = (string) $driver->CarNumber;
        $driverFullName = (string) $driver->Name;

        // Get or create entities with caching
        $entityCaches['carTypes'][$carTypeName] ??= $services['carType']->getCarTypeByName($carTypeName)
            ?? $services['carType']->createCarType(['car_type_name' => $carTypeName]);

        $entityCaches['carClasses'][$carClassName] ??= $services['carClass']->getCarClassByName($carClassName)
            ?? $services['carClass']->createCarClass(['car_class_name' => $carClassName]);

        $entityCaches['teams'][$teamName] ??= $services['team']->getTeamByName($teamName)
            ?? $services['team']->createTeam(['team_name' => $teamName]);

        $entityCaches['cars'][$carNumber] ??= $services['car']->getCarByCarNumber($carNumber)
            ?? $services['car']->createCar([
                'car_number' => $carNumber,
                'car_type_id' => $entityCaches['carTypes'][$carTypeName]->id,
                'car_class_id' => $entityCaches['carClasses'][$carClassName]->id,
                'team_id' => $entityCaches['teams'][$teamName]->id,
            ]);

        $entityCaches['drivers'][$driverFullName] ??= $services['driver']->getDriverByFullName($driverFullName)
            ?? $services['driver']->createDriver([
                'full_name' => $driverFullName,
                'is_player' => $driver->isPlayer,
            ]);

        return [
            'car' => $entityCaches['cars'][$carNumber],
            'driver' => $entityCaches['drivers'][$driverFullName],
        ];
    }

    protected function createOrGetSessionParticipation(\SimpleXMLElement $driver, LmuSession $lmuSession, array $entities, array $services)
    {
        $sessionData = [
            'lmu_session_id' => $lmuSession->id,
            'driver_id' => $entities['driver']->id,
            'car_id' => $entities['car']->id,
            'finish_position' => (int) $driver->Position,
            'class_finish_position' => (int) $driver->ClassPosition,
            'laps_completed' => (int) $driver->Laps,
            'pit_stops_executed' => (int) $driver->Pitstops,
            'best_lap_time' => (float) $driver->BestLapTime,
            'finish_status' => (string) $driver->FinishStatus,
            'dnf_reason' => (string) $driver->DNFReason,
        ];

        return $services['sessionParticipation']->getLmuSessionParticipation($sessionData)
            ?? $services['sessionParticipation']->createLmuSessionParticipation($sessionData);
    }

    protected function createRaceParticipation(\SimpleXMLElement $driver, $lmuSessionParticipation, array $services): void
    {
        $raceData = [
            'lmu_session_participation_id' => $lmuSessionParticipation->id,
            'grid_position' => (int) $driver->GridPos,
            'class_grid_position' => (int) $driver->ClassGridPos,
            'finish_time' => (float) $driver->FinishTime,
        ];

        $existingRaceParticipation = $services['raceParticipation']->getLmuRaceSessionParticipation($raceData);
        if (!$existingRaceParticipation) {
            $services['raceParticipation']->createLmuRaceSessionParticipation($raceData);
        }
    }

    protected function processDriverLaps(\SimpleXMLElement $driver, $lmuSessionParticipation): void
    {
        if (!isset($driver->Lap)) {
            return;
        }

        $lapServices = $this->initializeLapServices();

        foreach ($driver->Lap as $lap) {
            try {
                $compound = $this->getOrCreateCompound($lap, $lapServices['compound']);
                $lapRecord = $this->createLapRecord($lap, $lmuSessionParticipation, $compound, $lapServices['lap']);
                $this->createSectorRecords($lap, $lapRecord, $lapServices['sector']);
            } catch (\Exception $e) {
                Log::error("Error processing lap data", [
                    'lap_number' => (int) $lap['num'],
                    'error' => $e->getMessage(),
                    'filePath' => $this->filePath
                ]);
            }
        }
    }

    protected function initializeLapServices(): array
    {
        return [
            'lap' => app(\App\Services\LmuLapService::class),
            'compound' => app(\App\Services\LmuCompoundService::class),
            'sector' => app(\App\Services\LmuLapSectorService::class),
        ];
    }

    protected function getOrCreateCompound(\SimpleXMLElement $lap, $compoundService)
    {
        $compoundData = [
            'front_compound' => (string) $lap['fcompound'],
            'rear_compound' => (string) $lap['rcompound'],
        ];

        return $compoundService->getLmuCompound($compoundData)
            ?? $compoundService->createLmuCompound($compoundData);
    }

    protected function createLapRecord(\SimpleXMLElement $lap, $lmuSessionParticipation, $compound, $lapService)
    {
        $lapData = [
            'lmu_session_participation_id' => $lmuSessionParticipation->id,
            'lmu_compound_id' => $compound->id,
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

        return $lapService->getLmuLap($lapData)
            ?? $lapService->createLmuLap($lapData);
    }

    protected function createSectorRecords(\SimpleXMLElement $lap, $lapRecord, $sectorService): void
    {
        $sectorTimes = [
            1 => (float) $lap['s1'],
            2 => (float) $lap['s2'],
            3 => (float) $lap['s3'],
        ];

        foreach ($sectorTimes as $sectorNumber => $sectorTime) {
            $sectorData = [
                'lmu_lap_id' => $lapRecord->id,
                'sector_number' => $sectorNumber,
                'sector_time' => $sectorTime,
            ];

            $existingSector = $sectorService->getLmuLapSector($sectorData);
            if (!$existingSector) {
                $sectorService->createLmuLapSector($sectorData);
            }
        }
    }
}