<?php

namespace App\Services;

use App\Models\LmuSessionType;

/**
 * Service de gestion des types de session (SessionType).
 *
 * Cette classe fournit des méthodes pour créer ou gérer des types de session
 * dans la base de données, en encapsulant la logique métier liée aux types de session.
 */

class LmuSessionTypeService
{
    /**
     * Crée un nouveau type de session dans la base de données.
     *
     * Cette méthode vérifie que toutes les données requises sont présentes
     * avant de créer un nouvel objet `SessionType`. Si un champ obligatoire est
     * manquant, une exception est levée.
     *
     * ### Champs obligatoires :
     * - `name` (string) : Nom du type de session.
     *
     * @param array $data Données nécessaires à la création du type de session.
     *
     * @throws \InvalidArgumentException Si un champ obligatoire est manquant.
     *
     * @return LmuSessionType L'instance de `LmuSessionType` créée.
     */
    public function createLmuSessionType(array $data): LmuSessionType
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Name is required.');
        }

        return LmuSessionType::create([
            'name' => $data['name'],
        ]);
    }

    /**
     * Récupère un type de session à partir de son nom.
     *
     * Cette méthode recherche un type de session dans la base de données
     * correspondant au nom fourni.
     *
     * @param string $name Le nom du type de session à rechercher.
     *
     * @return LmuSessionType|null L'instance de `LmuSessionType` trouvée ou null si non trouvée.
     */
    public function getLmuSessionTypeByName(string $name): ?LmuSessionType
    {
        return LmuSessionType::where('name', $name)->first();
    }
}