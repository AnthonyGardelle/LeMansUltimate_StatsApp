<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{

    public function createResult($type, $startingAt, $duration, $track, $nb_drivers, $user_id)
    {
        if ($this->getResult($type, $startingAt, $duration, $track, $nb_drivers, $user_id)) {
            return null;
        } else {
            $result = new Result();
            $result->type = $type;
            $result->starting_at = $startingAt;
            $result->duration = $duration;
            $result->track = $track;
            $result->nb_drivers = $nb_drivers;
            $result->user_id = $user_id;
            $result->save();
            return $result;
        }
    }

    public function getResult($type, $startingAt, $duration, $track, $nb_drivers, $user_id)
    {
        $result = Result::where('type', $type)
            ->where('starting_at', $startingAt)
            ->where('duration', $duration)
            ->where('track', $track)
            ->where('nb_drivers', $nb_drivers)
            ->where('user_id', $user_id)
            ->first();
        if ($result) {
            return $result;
        }
        return null;
    }

    public function getAllResultsPaginate()
    {
        return Result::query()->orderBy('starting_at', 'desc')->paginate(10);
    }

    public function getAllResultsPaginateByUser($user)
    {
        return Result::query()->where('user_id', $user->id)->orderBy('starting_at', 'desc')->paginate(10);
    }

    public function loadAllResult($resultsPath, $user)
    {
        if (empty($user)) {
            return back()->withErrors(['error' => __('message.error')]);
        }

        if (empty($resultsPath) || !is_dir($resultsPath)) {
            return back()->withErrors(['error' => __('message.error')]);
        }

        $files = scandir($resultsPath);
        $xmlFiles = array_filter($files, fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'xml');

        if (!empty($xmlFiles)) {
            foreach ($xmlFiles as $xmlFile) {
                $this->init("{$resultsPath}/{$xmlFile}", $user);
            }
            return 1;
        } else {
            return back()->withErrors(['error' => __('message.error')]);
        }

    }

    public function showResults()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();

        $results = $this->getAllResultsPaginateByUser($user);
        return view('results', ["results" => $results]);
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

    public function init($file, $user)
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

        if (!$this->getResult($session_type, $timeString, $minutes, $trackCourse, $nb_drivers, $user->id)) {
            $this->createResult($session_type, $timeString, $minutes, $trackCourse, $nb_drivers, $user->id);
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
