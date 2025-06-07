<?php

namespace App\Services;

use App\Models\LmuRaceSessionParticipation;

class LmuRaceSessionParticipationService
{
    /**
     * Liste des champs obligatoires pour une participation à une session de course.
     */
    private const REQUIRED_FIELDS = [
        'lmu_session_participation_id',
        'grid_position',
        'class_grid_position',
        'finish_time',
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
     * Crée une nouvelle participation à une session de course.
     */
    public function createLmuRaceSessionParticipation(array $data): LmuRaceSessionParticipation
    {
        $this->validateRequiredFields($data);

        return LmuRaceSessionParticipation::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère une participation à une session de course existante.
     */
    public function getLmuRaceSessionParticipation(array $data): ?LmuRaceSessionParticipation
    {
        $this->validateRequiredFields($data);

        return LmuRaceSessionParticipation::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))->first();
    }
}