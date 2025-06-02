<?php

namespace App\Services;

use App\Models\LmuSessionParticipation;

class LmuSessionParticipationService
{
    /**
     * Liste des champs obligatoires pour une participation à une session.
     */
    private const REQUIRED_FIELDS = [
        'lmu_session_id',
        'driver_id',
        'car_id',
        'finish_position',
        'class_finish_position',
        'laps_completed',
        'pit_stops_executed',
        'best_lap_time',
        'finish_status',
        'dnf_reason',
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
     * Crée une nouvelle participation à une session.
     */
    public function createLmuSessionParticipation(array $data): LmuSessionParticipation
    {
        $this->validateRequiredFields($data);

        return LmuSessionParticipation::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère une participation à une session existante.
     */
    public function getLmuSessionParticipation(array $data): ?LmuSessionParticipation
    {
        $this->validateRequiredFields($data);

        return LmuSessionParticipation::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))->first();
    }
}