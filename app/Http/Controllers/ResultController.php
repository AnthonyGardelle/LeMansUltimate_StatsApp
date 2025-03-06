<?php

namespace App\Http\Controllers;

use App\Models\Result;

class ResultController extends Controller
{
    private $PATH_TO_RESULTS_ROOT = 'C:/Program Files (x86)/Steam/steamapps/common/Le Mans Ultimate/UserData/Log/Results/';

    public function createResult($type, $startingAt, $duration, $track)
    {
        if ($this->getResult($type, $startingAt, $duration, $track)) {
            return null;
        } else {
            $result = new Result();
            $result->type = $type;
            $result->starting_at = $startingAt;
            $result->duration = $duration;
            $result->track = $track;
            $result->save();
            return $result;
        }
    }

    public function getResult($type, $startingAt, $duration, $track)
    {
        $result = Result::where('type', $type)
            ->where('starting_at', $startingAt)
            ->where('duration', $duration)
            ->where('track', $track)
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
        return Result::all();
    }

    public function loadAllResult()
    {
        $files = scandir($this->PATH_TO_RESULTS_ROOT);
        $xmlFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'xml';
        });
        if (!empty($xmlFiles)) {
            foreach ($xmlFiles as $xmlFile) {
                $this->init("{$this->PATH_TO_RESULTS_ROOT}{$xmlFile}");
            }

            $results = $this->getAllResults();

            return view('results', ["results" => $results]);
        }
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

        $session_type = 'Unknown';

        if (isset($xml->RaceResults->Qualify)) {
            $session_type = 'Qualify';
        } elseif (isset($xml->RaceResults->Practice1)) {
            $session_type = 'Practice';
        } elseif (isset($xml->RaceResults->Race)) {
            $session_type = 'Race';
        }

        if (
            $this->getResult(
                $session_type,
                isset($xml->RaceResults->Qualify)
                ? $xml->RaceResults->Qualify->TimeString
                : (isset($xml->RaceResults->Practice1)
                    ? $xml->RaceResults->Practice1->TimeString
                    : $xml->RaceResults->Race->TimeString),
                isset($xml->RaceResults->Qualify)
                ? $xml->RaceResults->Qualify->Minutes
                : (isset($xml->RaceResults->Practice1)
                    ? $xml->RaceResults->Practice1->Minutes
                    : $xml->RaceResults->Race->Minutes),
                $xml->RaceResults->TrackCourse
            )
        ) {
        } else {
            $this->createResult(
                $session_type,
                isset($xml->RaceResults->Qualify)
                ? $xml->RaceResults->Qualify->TimeString
                : (isset($xml->RaceResults->Practice1)
                    ? $xml->RaceResults->Practice1->TimeString
                    : $xml->RaceResults->Race->TimeString),
                isset($xml->RaceResults->Qualify)
                ? $xml->RaceResults->Qualify->Minutes
                : (isset($xml->RaceResults->Practice1)
                    ? $xml->RaceResults->Practice1->Minutes
                    : $xml->RaceResults->Race->Minutes),
                $xml->RaceResults->TrackCourse
            );
        }
    }
}
