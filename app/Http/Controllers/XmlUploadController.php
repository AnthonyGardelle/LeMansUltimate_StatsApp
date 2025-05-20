<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessXmlFile;
use Illuminate\Support\Facades\Cache;

class XmlUploadController extends Controller
{
    public function upload(Request $request)
    {
        $files = $request->file('xml_files', []);

        $validFiles = [];
        foreach ($files as $file) {
            if ($file->extension() === 'xml') {
                $validFiles[] = $file;
            }
        }

        if (empty($validFiles)) {
            return response()->json(['message' => 'Aucun fichier XML valide.'], 422);
        }

        foreach ($validFiles as $file) {
            $infos = [
                "user_id" => auth()->id(),
            ];
            $path = $file->store('uploads', 'public');
            ProcessXmlFile::dispatch($path, $infos);
        }

        Cache::increment('upload_total_' . auth()->id(), count($validFiles));

        return response()->json(['message' => count($validFiles) . ' fichiers XML valides uploadés et en file d\'attente.']);
    }
}
