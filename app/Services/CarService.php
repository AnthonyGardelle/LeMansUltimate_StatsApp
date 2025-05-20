<?php

namespace App\Services;

use App\Models\Car;

/**
 * Service de gestion des enregistrements de voitures (Car).
 *
 * Cette classe fournit des méthodes pour créer ou gérer des enregistrements
 * de voitures dans la base de données, en encapsulant la logique métier
 * liée aux voitures.
 */
class CarService
{
    /**
     * Crée un nouvel enregistrement de voiture dans la base de données.
     *
     * Cette méthode vérifie que toutes les données requises sont présentes
     * avant de créer un nouvel objet `Car`. Si un champ obligatoire est
     * manquant, une exception est levée.
     *
     * ### Champs obligatoires :
     * - `car_number` (string) : Numéro de la voiture (unique).
     * - `car_type_id` (int) : ID du type de voiture (clé étrangère).
     * - `car_class_id` (int) : ID de la classe de voiture (clé étrangère).
     * - `team_id` (int) : ID de l'équipe associée (clé étrangère).
     *
     * @param array $data Les données nécessaires à la création de la voiture.
     *
     * @throws \InvalidArgumentException Si un champ requis est manquant.
     *
     * @return Car L'instance `Car` créée.
     */
    public function createCar(array $data): Car
    {
        if (empty($data['car_number'])) {
            throw new \InvalidArgumentException('Car number is required.');
        }

        if (empty($data['car_type_id'])) {
            throw new \InvalidArgumentException('Car type ID is required.');
        }

        if (empty($data['car_class_id'])) {
            throw new \InvalidArgumentException('Car class ID is required.');
        }

        if (empty($data['team_id'])) {
            throw new \InvalidArgumentException('Team ID is required.');
        }

        return Car::create([
            'car_number' => $data['car_number'],
            'car_type_id' => $data['car_type_id'],
            'car_class_id' => $data['car_class_id'],
            'team_id' => $data['team_id'],
        ]);
    }

    /**
     * Récupère une voiture à partir de son numéro.
     *
     * Cette méthode recherche dans la base de données une instance de `Car`
     * correspondant au numéro fourni. Elle renvoie la première correspondance
     * trouvée ou `null` si aucune voiture n'est trouvée.
     *
     * @param string $carNumber Le numéro de la voiture à rechercher.
     *
     * @return Car|null L'instance de `Car` trouvée ou `null` si aucune correspondance.
     */
    public function getCarByCarNumber(string $carNumber): ?Car
    {
        return Car::where('car_number', $carNumber)->first();
    }
}
