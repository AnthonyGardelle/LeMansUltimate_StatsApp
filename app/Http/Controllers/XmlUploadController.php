<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessXmlFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class XmlUploadController extends Controller
{
    public function upload(Request $request)
    {
        $files = $request->file('xml_files', []);

        // Si $files est un tableau de tableaux (upload multiple), on l’aplatit
        $flattened = [];

        foreach ($files as $fileItem) {
            if (is_array($fileItem)) {
                foreach ($fileItem as $subFile) {
                    $flattened[] = $subFile;
                }
            } else {
                $flattened[] = $fileItem;
            }
        }

        $validFiles = [];

        foreach ($flattened as $file) {
            if (
                $file->extension() === 'xml' &&
                in_array($file->getMimeType(), ['text/xml', 'application/xml', 'text/plain'])
            ) {
                $validFiles[] = $file;
            }
        }

        if (empty($validFiles)) {
            return response()->json(['message' => 'Aucun fichier XML valide.'], 422);
        }

        foreach ($validFiles as $file) {
            $infos = ["user_id" => auth()->id()];
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');

            ProcessXmlFile::dispatch($path, $infos);
        }

        Cache::increment('upload_total_' . auth()->id(), count($validFiles));

        return response()->json([
            'message' => count($validFiles) . ' fichiers XML valides uploadés et en file d\'attente.'
        ]);
    }
}
