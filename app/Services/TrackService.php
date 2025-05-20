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
     * Crée un nouveau circuit dans la base de données.
     *
     * Cette méthode vérifie que les champs requis `track_venue`, `track_course`,
     * `track_event` et `track_length` sont présents avant de créer un nouvel
     * enregistrement dans la table `tracks`.
     *
     * ### Champs obligatoires :
     * - `track_venue` (string) : Lieu du circuit.
     * - `track_course` (string) : Nom du circuit.
     * - `track_event` (string) : Événement associé au circuit.
     * - `track_length` (float) : Longueur du circuit.
     *
     * @param array $data Données nécessaires à la création du circuit.
     *
     * @throws \InvalidArgumentException Si l'un des champs requis est manquant.
     *
     * @return Track L'instance de `Track` créée.
     */
    public function createTrack(array $data): Track
    {
        if (empty($data['track_venue']) || empty($data['track_course']) || empty($data['track_event']) || empty($data['track_length'])) {
            throw new \InvalidArgumentException('All track fields are required.');
        }

        return Track::create([
            'track_venue' => $data['track_venue'],
            'track_course' => $data['track_course'],
            'track_event' => $data['track_event'],
            'track_length' => $data['track_length']
        ]);
    }

    /**
     * Récupère un circuit à partir de ses détails.
     *
     * Cette méthode recherche dans la base de données un enregistrement `Track`
     * correspondant aux détails fournis. Elle renvoie la première correspondance
     * trouvée ou `null` si aucune correspondance n'existe.
     *
     * @param string $trackVenue Lieu du circuit.
     * @param string $trackCourse Nom du circuit.
     * @param string $trackEvent Événement associé au circuit.
     * @param float $trackLength Longueur du circuit.
     *
     * @return Track|null L'instance de `Track` trouvée ou `null`.
     */
    public function getTrack(string $trackVenue, string $trackCourse, string $trackEvent, float $trackLength): ?Track
    {
        return Track::where('track_venue', $trackVenue)
            ->where('track_course', $trackCourse)
            ->where('track_event', $trackEvent)
            ->where('track_length', $trackLength)
            ->first();
    }
}