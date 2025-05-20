<?php

namespace App\Services;

use App\Models\CarType;

/**
 * Service de gestion des types de voitures (CarType).
 *
 * Cette classe fournit des méthodes pour créer et récupérer des types de voitures
 * dans la base de données. Elle encapsule la logique métier associée aux entités
 * `CarType`.
 */
class CarTypeService
{
    /**
     * Crée un nouveau type de voiture dans la base de données.
     *
     * Cette méthode vérifie que le champ requis `car_type_name` est présent
     * avant de créer un nouvel enregistrement dans la table `car_types`.
     *
     * ### Champ obligatoire :
     * - `car_type_name` (string) : Nom du type de voiture.
     *
     * @param array $data Données nécessaires à la création du type de voiture.
     *
     * @throws \InvalidArgumentException Si le champ `car_type_name` est manquant.
     *
     * @return CarType L'instance de `CarType` créée.
     */
    public function createCarType(array $data): CarType
    {
        if (empty($data['car_type_name'])) {
            throw new \InvalidArgumentException('Car type name is required.');
        }

        return CarType::create([
            'car_type_name' => $data['car_type_name']
        ]);
    }

    /**
     * Récupère un type de voiture à partir de son nom.
     *
     * Cette méthode recherche dans la base de données un enregistrement `CarType`
     * correspondant au nom fourni. Elle renvoie la première correspondance
     * trouvée ou `null` si aucune correspondance n'existe.
     *
     * @param string $carTypeName Le nom du type de voiture à rechercher.
     *
     * @return CarType|null L'instance de `CarType` trouvée ou `null`.
     */
    public function getCarTypeByName(string $carTypeName): ?CarType
    {
        return CarType::where('car_type_name', $carTypeName)->first();
    }
}
