<?php

namespace App\Services;

use App\Models\LmuLap;

class LmuLapService
{

    /**
     * Liste des champs obligatoires pour un tour.
     */
    private const REQUIRED_FIELDS = [
        'lmu_session_participation_id',
        'lap_number',
        'finish_position',
        'lap_time',
        'top_speed',
        'remaining_fuel',
        'fuel_used',
        'remaining_virtual_energy',
        'virtual_energy_used',
        'tire_wear_fl',
        'tire_wear_fr',
        'tire_wear_rl',
        'tire_wear_rr',
    ];

    /**
     * Valide que tous les champs obligatoires sont présents dans les données.
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateRequiredFields(array $data): void
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("Field '$field' is required.");
            }
        }
    }

    /**
     * Crée un nouveau tour.
     */
    public function createLmuLap(array $data): LmuLap
    {
        $this->validateRequiredFields($data);

        return LmuLap::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère un tour existant.
     */
    public function getLmuLap(array $data): ?LmuLap
    {
        $this->validateRequiredFields($data);

        return LmuLap::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))
            ->first();
    }
}