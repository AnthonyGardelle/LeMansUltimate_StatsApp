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
        $invalidFiles = [];

        foreach ($files as $file) {
            if ($file->extension() !== 'xml' || $file->getMimeType() !== 'text/xml') {
                $invalidFiles[] = $file->getClientOriginalName() . ' (extension/mime invalide)';
                continue;
            }

            $content = file_get_contents($file->getRealPath());
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($content);
            if ($xml === false) {
                $errors = libxml_get_errors();
                $invalidFiles[] = $file->getClientOriginalName() . ' (XML mal formé)';
                libxml_clear_errors();
                continue;
            }

            $validFiles[] = $file;
        }

        if (empty($validFiles)) {
            return response()->json([
                'message' => 'Aucun fichier XML valide.',
                'invalid_files' => $invalidFiles
            ], 422);
        }

        foreach ($validFiles as $file) {
            $infos = ["user_id" => auth()->id()];
            $path = $file->store('uploads', 'public');
            ProcessXmlFile::dispatch($path, $infos);
        }

        Cache::increment('upload_total_' . auth()->id(), count($validFiles));

        return response()->json([
            'message' => count($validFiles) . ' fichiers XML valides uploadés.',
            'invalid_files' => $invalidFiles
        ]);
    }
}
