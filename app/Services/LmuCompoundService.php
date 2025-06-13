<?php

namespace App\Services;

use App\Models\LmuCompound;

class LmuCompoundService
{
    /**
     * Liste des champs obligatoires pour un composé.
     */
    private const REQUIRED_FIELDS = [
        'front_compound',
        'rear_compound',
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
     * Crée un nouveau composé.
     */
    public function createLmuCompound(array $data): LmuCompound
    {
        $this->validateRequiredFields($data);

        return LmuCompound::create(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)));
    }

    /**
     * Récupère un composé existant.
     */
    public function getLmuCompound(array $data): ?LmuCompound
    {
        $this->validateRequiredFields($data);

        return LmuCompound::where(array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)))->first();
    }
}