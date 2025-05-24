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
        foreach ($files as $index => $file) {
            if ($file->extension() === 'xml' && $file->getMimeType() === 'text/xml') {
                $validFiles[] = [
                    'file' => $file,
                    'last_modified' => $request->input("file_last_modified_{$index}"),
                    'original_name' => $request->input("file_name_{$index}")
                ];
            }
        }

        if (empty($validFiles)) {
            return response()->json(['message' => 'Aucun fichier XML valide.'], 422);
        }

        foreach ($validFiles as $fileData) {
            $file = $fileData['file'];
            $lastModified = $fileData['last_modified'] ?
                \Carbon\Carbon::createFromTimestamp($fileData['last_modified'] / 1000) :
                null;

            $infos = [
                "user_id" => auth()->id(),
                "original_filename" => $file->getClientOriginalName(),
                "last_modified_client" => $lastModified,
                "uploaded_at" => now(),
            ];

            $path = $file->store('uploads', 'public');
            ProcessXmlFile::dispatch($path, $infos);
        }

        Cache::increment('upload_total_' . auth()->id(), count($validFiles));

        return response()->json([
            'message' => count($validFiles) . ' fichiers XML valides uploadés et en file d\'attente.'
        ]);
    }
}
