<?php

namespace App\Services;

use App\Models\LmuLapSector;

class LmuLapSectorService
{
    /**
     * Liste des champs obligatoires pour un secteur de tour.
     */
    private const REQUIRED_FIELDS = [
        'lmu_lap_id',
        'sector_number',
        'sector_time',
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
     * Crée un nouveau secteur de tour.
     */
    public function createLmuLapSector(array $data): LmuLapSector
    {
        $this->validateRequiredFields($data);

        return LmuLapSector::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère un secteur de tour existant.
     */
    public function getLmuLapSector(array $data): ?LmuLapSector
    {
        $this->validateRequiredFields($data);

        return LmuLapSector::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))->first();
    }
}