<?php

namespace App\Services;

use App\Models\Team;

/**
 * Service de gestion des équipes (Team).
 * 
 * Cette classe fournit des méthodes pour créer et récupérer des équipes
 * dans la base de données. Elle encapsule la logique métier associée aux entités
 * `Team`.
 */
class TeamService
{
    /**
     * Crée une nouvelle équipe dans la base de données.
     *
     * Cette méthode vérifie que le champ requis `team_name` est présent
     * avant de créer un nouvel enregistrement dans la table `teams`.
     *
     * ### Champ obligatoire :
     * - `team_name` (string) : Nom de l'équipe.
     *
     * @param array $data Données nécessaires à la création de l'équipe.
     *
     * @throws \InvalidArgumentException Si le champ `team_name` est manquant.
     *
     * @return Team L'instance de `Team` créée.
     */
    public function createTeam(array $data): Team
    {
        if (empty($data['team_name'])) {
            throw new \InvalidArgumentException('Team name is required.');
        }

        return Team::create([
            'team_name' => $data['team_name']
        ]);
    }

    /**
     * Récupère une équipe à partir de son nom.
     *
     * Cette méthode recherche dans la base de données un enregistrement `Team`
     * correspondant au nom fourni. Elle renvoie la première correspondance
     * trouvée ou `null` si aucune correspondance n'existe.
     *
     * @param string $teamName Le nom de l'équipe à rechercher.
     *
     * @return Team|null L'instance de `Team` trouvée ou `null`.
     */
    public function getTeamByName(string $teamName): ?Team
    {
        return Team::where('team_name', $teamName)->first();
    }
}