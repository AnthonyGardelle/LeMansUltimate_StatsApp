<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;

class ProcessXmlFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected array $infos;
    protected Carbon $startTime;

    public function __construct(string $filePath, array $infos)
    {
        Log::info('Queue hit contract()');
        $this->filePath = $filePath;
        $this->infos = $infos;
        $this->startTime = now();
    }

    public function retryUntil(): Carbon
    {
        // Retenter jusqu’à 10 secondes après le dispatch
        return now()->addSeconds(10);
    }

    public function handle(): void
    {
        Log::info('Starting XML processing', [
            'filePath' => $this->filePath,
            'userId' => $this->infos['user_id'],
        ]);
        try {
            $xml = $this->loadXmlOrFail();

            Cache::increment('upload_progress_' . $this->infos['user_id']);
        } catch (\Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
        Log::info('XML processed successfully', [
            'filePath' => $this->filePath,
            'userId' => $this->infos['user_id'],
        ]);
    }

    protected function loadXmlOrFail(): \SimpleXMLElement
    {
        $path = storage_path("app/public/{$this->filePath}");

        if (!file_exists($path)) {
            throw new \Exception("Fichier XML introuvable (retry demandé)");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Échec lecture fichier XML (retry demandé)");
        }

        return $this->parseXmlContent($content);
    }

    protected function parseXmlContent(string $content): \SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            Cache::decrement('upload_total_' . $this->infos['user_id']);
            throw new \Exception("Contenu XML invalide");
        }

        return $xml;
    }

    public function failed(Throwable $e)
    {
        Log::error('Queue failed: ' . $e?->getMessage() ?? 'Unknown error');
    }
}