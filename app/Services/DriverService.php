<?php

namespace App\Services;

use App\Models\Driver;

/**
 * Service de gestion des enregistrements de pilotes (Driver).
 *
 * Cette classe fournit des méthodes pour créer ou gérer des enregistrements
 * de pilotes dans la base de données, en encapsulant la logique métier
 * liée aux pilotes.
 */
class DriverService
{
    /**
     * Crée un nouvel enregistrement de pilote dans la base de données.
     *
     * Cette méthode vérifie que toutes les données requises sont présentes
     * avant de créer un nouvel objet `Driver`. Si un champ obligatoire est
     * manquant, une exception est levée.
     *
     * ### Champs obligatoires :
     * - `full_name` (string) : Nom complet du pilote.
     * - `is_player` (boolean) : Indique si le pilote est un joueur.
     * 
     * @param array $data Données nécessaires à la création du pilote.
     * 
     * @throws \InvalidArgumentException Si un champ obligatoire est manquant.
     * 
     * @return Driver L'instance de `Driver` créée.
     */
    public function createDriver(array $data): Driver
    {
        if (empty($data['full_name'])) {
            throw new \InvalidArgumentException('Full name is required.');
        }

        if (!isset($data['is_player'])) {
            throw new \InvalidArgumentException('is_player is required.');
        }

        $nameParts = explode(' ', $data['full_name']);
        $firstName = array_shift($nameParts);
        $lastName = implode(' ', $nameParts);

        return Driver::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'is_player' => $data['is_player'],
        ]);
    }

    /**
     * Récupère un pilote à partir de son nom complet.
     * 
     * Cette méthode recherche un pilote dans la base de données
     * correspondant au nom complet fourni.
     * 
     * @param string $fullName Le nom complet du pilote à rechercher.
     * 
     * @return Driver|null L'instance `Driver` trouvée ou `null`.
     */
    public function getDriverByFullName(string $fullName): ?Driver
    {
        return Driver::whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$fullName])->first();
    }
}