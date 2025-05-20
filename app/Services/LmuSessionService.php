<?php

namespace App\Services;

use App\Models\LmuSession;

class LmuSessionService
{
    /**
     * Liste des champs obligatoires pour une session.
     */
    private const REQUIRED_FIELDS = [
        'lmu_session_type_id',
        'track_id',
        'starting_at',
        'duration',
        'mech_fail_rate',
        'damage_multiplier',
        'fuel_multiplier',
        'tire_multiplier',
        'parc_ferme',
        'fixed_setups',
        'free_settings',
        'fixed_upgrades',
        'limited_tires',
        'tire_warmers',
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
     * Crée une nouvelle session.
     */
    public function createLmuSession(array $data): LmuSession
    {
        $this->validateRequiredFields($data);

        return LmuSession::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère une session existante.
     */
    public function getLmuSession(array $data): ?LmuSession
    {
        $this->validateRequiredFields($data);

        return LmuSession::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))
            ->first();
    }
}