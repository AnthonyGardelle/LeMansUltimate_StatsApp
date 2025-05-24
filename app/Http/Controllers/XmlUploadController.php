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
            if ($file->extension() === 'xml' && $file->getMimeType() === 'text/xml') {
                $filename = $file->getClientOriginalName();
                $lastModifiedKey = 'last_modified_' . $filename;
                $lastModified = $request->input($lastModifiedKey);
                $validFiles[] = [
                    'file' => $file,
                    'last_modified' => $lastModified,
                ];
            }
        }

        if (empty($validFiles)) {
            return response()->json(['message' => 'Aucun fichier XML valide.'], 422);
        }

        foreach ($validFiles as $entry) {
            $file = $entry['file'];
            $lastModified = $entry['last_modified'];

            $path = $file->store('uploads', 'public');

            $infos = [
                "user_id" => auth()->id(),
                "last_modified_user" => $lastModified,
            ];

            ProcessXmlFile::dispatch($path, $infos);
        }

        Cache::increment('upload_total_' . auth()->id(), count($validFiles));

        return response()->json(['message' => count($validFiles) . ' fichiers XML valides uploadés et en file d\'attente.']);
    }
}
