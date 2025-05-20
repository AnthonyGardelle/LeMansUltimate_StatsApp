<?php

namespace App\Services;

use App\Models\CarClass;

/**
 * Service de gestion des enregistrements de classes de voitures (CarClass).
 * 
 * Cette classe fournit des méthodes pour créer ou gérer des enregistrements
 * de classes de voitures dans la base de données, en encapsulant la logique métier
 * liée aux classes de voitures.
 */
class CarClassService
{
    /**
     * Crée un nouvel enregistrement de classe de voiture dans la base de données.
     * 
     * Cette méthode vérifie que toutes les données requises sont présentes
     * avant de créer un nouvel objet `CarClass`. Si un champ obligatoire est
     * manquant, une exception est levée.
     * 
     * ### Champs obligatoires :
     * - `car_class_name` (string) : Nom de la classe de voiture.
     * 
     * @param array $data Les données nécessaires à la création de la classe de voiture.
     * 
     * @throws \InvalidArgumentException Si un champ requis est manquant.
     * 
     * @return CarClass L'instance `CarClass` créée.
     */
    public function createCarClass(array $data): CarClass
    {
        if (empty($data['car_class_name'])) {
            throw new \InvalidArgumentException('Car class name is required.');
        }

        return CarClass::create([
            'car_class_name' => $data['car_class_name'],
        ]);
    }

    /**
     * Récupère une classe de voiture à partir de son nom.
     * 
     * Cette méthode recherche dans la base de données un enregistrement `CarClass`
     * correspondant au nom fourni. Elle renvoie la première correspondance
     * trouvée ou `null` si aucune correspondance n'existe.
     * 
     * @param string $carClassName Le nom de la classe de voiture à rechercher.
     * 
     * @return CarClass|null L'instance de `CarClass` trouvée ou `null`.
     */
    public function getCarClassByName(string $carClassName): ?CarClass
    {
        return CarClass::where('car_class_name', $carClassName)->first();
    }
}