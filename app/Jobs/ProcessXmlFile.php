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
        Log::info('Queue job constructed', [
            'filePath' => $filePath,
            'userId' => $infos['user_id'] ?? 'unknown'
        ]);

        $this->filePath = $filePath;
        $this->infos = $infos;
        $this->startTime = now();
    }

    public function handle(): void
    {
        $userId = $this->infos['user_id'] ?? 'unknown';

        Log::info('Starting XML processing', [
            'filePath' => $this->filePath,
            'userId' => $userId,
        ]);

        $xml = $this->loadXmlOrFail();

        // Add your actual XML processing logic here
        // For now, just incrementing progress
        Cache::increment("upload_progress_{$userId}");

        Log::info('XML processed successfully', [
            'filePath' => $this->filePath,
            'userId' => $userId,
            'processingTime' => now()->diffInSeconds($this->startTime) . 's'
        ]);
    }

    protected function loadXmlOrFail(): \SimpleXMLElement
    {
        $path = storage_path("app/public/{$this->filePath}");

        if (!file_exists($path)) {
            throw new \Exception("XML file not found: {$path}");
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Failed to read XML file: {$path}");
        }

        return $this->parseXmlContent($content);
    }

    protected function parseXmlContent(string $content): \SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $errors = libxml_get_errors();
            $errorMessages = array_map(fn($error) => trim($error->message), $errors);
            libxml_clear_errors();

            // Clean up progress tracking on XML parsing failure
            $userId = $this->infos['user_id'] ?? 'unknown';
            Cache::decrement("upload_total_{$userId}");

            throw new \Exception("Invalid XML content: " . implode(', ', $errorMessages));
        }

        return $xml;
    }

    public function failed(Throwable $e): void
    {
        $userId = $this->infos['user_id'] ?? 'unknown';

        Log::error('XML processing job failed', [
            'error' => $e->getMessage(),
            'filePath' => $this->filePath,
            'userId' => $userId,
            'processingTime' => now()->diffInSeconds($this->startTime) . 's',
            'trace' => $e->getTraceAsString()
        ]);

        // Clean up progress tracking on failure
        Cache::decrement("upload_progress_{$userId}");
    }
}