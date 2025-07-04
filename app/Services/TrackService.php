<?php

namespace App\Services;

use App\Models\Track;

/**
 * Service de gestion des circuits (Track).
 * 
 * Cette classe fournit des méthodes pour créer et récupérer des circuits
 * dans la base de données. Elle encapsule la logique métier associée aux entités
 * `Track`.
 */
class TrackService
{
    /**
     * Champs obligatoires pour la création d'un circuit.
     */
    private const REQUIRED_FIELDS = [
        'track_venue',
        'track_course',
        'track_event',
        'track_length'
    ];

    /**
     * Valide que tous les champs obligatoires sont présents dans les données.
     *
     * @param array $data Les données à valider.
     * 
     * @throws \InvalidArgumentException Si un champ obligatoire est manquant.
     */
    private function validateRequiredFields(array $data): void
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Le champ '{$field}' est obligatoire.");
            }
        }
    }

    /**
     * Récupère un circuit existant ou en crée un nouveau.
     *
     * Cette méthode recherche d'abord un circuit avec les données fournies.
     * Si aucun n'est trouvé, elle en crée un nouveau.
     *
     * @param array $data Les données du circuit (doit contenir les champs obligatoires).
     *
     * @return Track L'instance de `Track` trouvée ou créée.
     *
     * @throws \InvalidArgumentException Si un champ obligatoire est manquant.
     */
    public function findOrCreateTrack(array $data): Track
    {
        $this->validateRequiredFields($data);

        return Track::firstOrCreate(
            array_intersect_key($data, array_flip(self::REQUIRED_FIELDS)),
            array_intersect_key($data, array_flip(self::REQUIRED_FIELDS))
        );
    }
}