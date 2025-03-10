<?php

namespace App\Http\Controllers;

use App\Models\Result;

class ResultController extends Controller
{
    private $PATH_TO_RESULTS_ROOT;

    public function __construct()
    {
        $this->PATH_TO_RESULTS_ROOT = session('results_path', config('app.results_path'));
    }

    public function createResult($type, $startingAt, $duration, $track, $nb_drivers)
    {
        if ($this->getResult($type, $startingAt, $duration, $track, $nb_drivers)) {
            return null;
        } else {
            $result = new Result();
            $result->type = $type;
            $result->starting_at = $startingAt;
            $result->duration = $duration;
            $result->track = $track;
            $result->nb_drivers = $nb_drivers;
            $result->save();
            return $result;
        }
    }

    public function getResult($type, $startingAt, $duration, $track, $nb_drivers)
    {
        $result = Result::where('type', $type)
            ->where('starting_at', $startingAt)
            ->where('duration', $duration)
            ->where('track', $track)
            ->where('nb_drivers', $nb_drivers)
            ->first();
        if ($result) {
            return $result;
        }
        return null;
    }

    public function updateResult($type, $newType, $startingAt, $newStartingAt, $duration, $newDuration, $track, $newTrack)
    {
        $result = Result::where('type', $type)
            ->where('starting_at', $startingAt)
            ->where('duration', $duration)
            ->where('track', $track)
            ->first();
        if ($result) {
            $result->type = $newType;
            $result->starting_at = $newStartingAt;
            $result->duration = $newDuration;
            $result->track = $newTrack;
            $result->save();
            return $result;
        }
        return null;
    }

    public function deleteResult($type, $startingAt, $duration, $track)
    {
        $result = Result::where('type', $type)
            ->where('starting_at', $startingAt)
            ->where('duration', $duration)
            ->where('track', $track)
            ->first();
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }

    public function getAllResults()
    {
        return Result::query()->orderBy('starting_at', 'desc')->get();
    }

    public function getAllResultsPaginate()
    {
        return Result::query()->orderBy('starting_at', 'desc')->paginate(10);
    }

    public function loadAllResult()
    {
        // Vérifie si le chemin des résultats est vide ou invalide
        if (empty($this->PATH_TO_RESULTS_ROOT) || !is_dir($this->PATH_TO_RESULTS_ROOT)) {
            return view('results')->with('error', 'Le chemin des résultats est invalide ou vide.');
        }

        $files = scandir($this->PATH_TO_RESULTS_ROOT);
        $xmlFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'xml';
        });

        if (!empty($xmlFiles)) {
            foreach ($xmlFiles as $xmlFile) {
                $this->init("{$this->PATH_TO_RESULTS_ROOT}/{$xmlFile}");
            }

            $results = $this->getAllResultsPaginate();

            return view('results', ["results" => $results]);
        } else {
            return view('results')->with('error', 'Aucun fichier XML trouvé dans le dossier des résultats.');
        }
    }


    public function getHowManyDriversFromXml($xml)
    {
        $cpt = 0;

        $session_type = $this->getSessionType($xml);

        if ($session_type === "Qualify") {
            $cpt = count($xml->RaceResults->Qualify->Driver);
        } elseif ($session_type === "Practice") {
            $cpt = count($xml->RaceResults->Practice1->Driver);
        } elseif ($session_type === "Race") {
            $cpt = count($xml->RaceResults->Race->Driver);
        }

        return $cpt;
    }

    public function init($file)
    {
        libxml_use_internal_errors(true);

        $xml = simplexml_load_file($file);

        if ($xml === false) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                // echo "Erreur XML: " . $error->message . " à la ligne " . $error->line . "<br>";
            }
            libxml_clear_errors();

            return null;
        }

        $session_type = $this->getSessionType($xml);
        $timeString = $this->getTimeString($xml);
        $minutes = $this->getMinutes($xml);
        $trackCourse = $xml->RaceResults->TrackCourse;
        $nb_drivers = $this->getHowManyDriversFromXml($xml);

        if (!$this->getResult($session_type, $timeString, $minutes, $trackCourse, $nb_drivers)) {
            $this->createResult($session_type, $timeString, $minutes, $trackCourse, $nb_drivers);
        }
    }

    private function getSessionType($xml)
    {
        if (isset($xml->RaceResults->Qualify)) {
            return 'Qualify';
        } elseif (isset($xml->RaceResults->Practice1)) {
            return 'Practice';
        } elseif (isset($xml->RaceResults->Race)) {
            return 'Race';
        }
        return 'Unknown';
    }

    private function getTimeString($xml)
    {
        if (isset($xml->RaceResults->Qualify)) {
            return $xml->RaceResults->Qualify->TimeString;
        } elseif (isset($xml->RaceResults->Practice1)) {
            return $xml->RaceResults->Practice1->TimeString;
        } elseif (isset($xml->RaceResults->Race)) {
            return $xml->RaceResults->Race->TimeString;
        }
        return null;
    }

    private function getMinutes($xml)
    {
        if (isset($xml->RaceResults->Qualify)) {
            return $xml->RaceResults->Qualify->Minutes;
        } elseif (isset($xml->RaceResults->Practice1)) {
            return $xml->RaceResults->Practice1->Minutes;
        } elseif (isset($xml->RaceResults->Race)) {
            return $xml->RaceResults->Race->Minutes;
        }
        return null;
    }
}
