<?php

namespace App\Services;

use App\Models\LmuSessionGroups;

class LmuSessionGroupService
{
    /**
     * Liste des champs obligatoires pour un groupe de session.
     */
    private const REQUIRED_FIELDS = [
        'starting_at',
        'hashcode',
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
     * Crée un nouveau groupe de session.
     */
    public function createLmuSessionGroup(array $data): LmuSessionGroups
    {
        $this->validateRequiredFields($data);

        return LmuSessionGroups::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère un groupe de session existant.
     */
    public function getLmuSessionGroup(array $data): ?LmuSessionGroups
    {
        $this->validateRequiredFields($data);

        return LmuSessionGroups::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))->first();
    }
}