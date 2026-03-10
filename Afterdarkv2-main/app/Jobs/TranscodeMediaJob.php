<?php

namespace App\Jobs;

use App\Models\MediaAsset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Throwable;

class TranscodeMediaJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public $timeout = 3600;

    public function __construct(
        public MediaAsset $mediaAsset,
        public string $outputFormat,
        public ?string $quality = null
    ) {}

    public function handle(): void
    {
        $this->mediaAsset->update(['status' => 'processing']);

        Log::info("Starting transcode job for MediaAsset {$this->mediaAsset->id}", [
            'asset_id' => $this->mediaAsset->id,
            'format' => $this->outputFormat,
            'quality' => $this->quality,
            'attempt' => $this->attempts(),
        ]);

        try {
            $inputPath = storage_path('app/' . $this->mediaAsset->path);
            $outputFilename = Str::uuid() . '.' . $this->outputFormat;
            $outputPath = storage_path('app/media/renditions/' . $outputFilename);

            if (!file_exists(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }

            $ffmpegPath = config('settings.ffmpeg_path', 'ffmpeg');

            $command = $this->buildFfmpegCommand($ffmpegPath, $inputPath, $outputPath);

            $result = Process::timeout($this->timeout)->run($command);

            if ($result->failed()) {
                throw new \RuntimeException('FFmpeg failed: ' . $result->errorOutput());
            }

            $rendition = MediaAsset::create([
                'uuid' => Str::uuid(),
                'parent_id' => $this->mediaAsset->id,
                'assetable_type' => $this->mediaAsset->assetable_type,
                'assetable_id' => $this->mediaAsset->assetable_id,
                'type' => 'rendition',
                'format' => $this->outputFormat,
                'quality' => $this->quality,
                'path' => 'media/renditions/' . $outputFilename,
                'filename' => $outputFilename,
                'file_size' => filesize($outputPath),
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            $this->mediaAsset->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            Log::info("Transcode job completed for MediaAsset {$this->mediaAsset->id}", [
                'asset_id' => $this->mediaAsset->id,
                'rendition_id' => $rendition->id,
                'output_path' => $outputPath,
            ]);
        } catch (Throwable $e) {
            Log::error("Transcode job failed for MediaAsset {$this->mediaAsset->id}", [
                'asset_id' => $this->mediaAsset->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            $this->mediaAsset->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            throw $e;
        }
    }

    protected function buildFfmpegCommand(string $ffmpegPath, string $inputPath, string $outputPath): string
    {
        $command = "{$ffmpegPath} -i " . escapeshellarg($inputPath);

        if ($this->outputFormat === 'mp3') {
            $bitrate = $this->quality === 'high' ? '320k' : '192k';
            $command .= " -codec:a libmp3lame -b:a {$bitrate}";
        } elseif ($this->outputFormat === 'aac') {
            $bitrate = $this->quality === 'high' ? '256k' : '128k';
            $command .= " -codec:a aac -b:a {$bitrate}";
        } elseif ($this->outputFormat === 'flac') {
            $command .= ' -codec:a flac';
        }

        $command .= ' ' . escapeshellarg($outputPath);

        return $command;
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Transcode job permanently failed for MediaAsset {$this->mediaAsset->id}", [
            'asset_id' => $this->mediaAsset->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        $this->mediaAsset->update([
            'status' => 'failed',
            'error_message' => "Job failed after {$this->attempts()} attempts: " . $exception->getMessage(),
            'processed_at' => now(),
        ]);
    }
}
