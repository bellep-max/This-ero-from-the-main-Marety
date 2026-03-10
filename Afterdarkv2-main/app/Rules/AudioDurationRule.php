<?php

namespace App\Rules;

use App\Models\Group;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\FFProbe;
use Symfony\Component\Process\Process;

class AudioDurationRule implements ValidationRule
{
    public function validate($attribute, $value, Closure $fail): void
    {
        $durationLimit = Group::getValue('duration_limit') ?? config('settings.podcast_min_audio_duration');

        $process = new Process([app()->environment('local') ? config('media-library.ffprobe_path') : 'ffprobe', '-i', $value, '-show_entries', 'format=duration', '-v', 'quiet', '-of', 'csv=p=0']);

        $process->run();

        // todo fix this rule
        //        if (!File::isDirectory('temp_uploads')) {
        //            Storage::disk('local')->makeDirectory('temp_uploads');
        //        }
        //
        //        $tempPath = $value->storePublicicly('temp_uploads', 'local');
        //        $fullTempPath = Storage::disk('local')->path($tempPath);
        //
        //        $filePath = $value->getRealPath();
        //
        //        if (!file_exists($filePath) || !is_readable($filePath)) {
        //            Log::error("FFProbe: Temporary file not found or not readable: " . $filePath);
        //            $fail('The :attribute file could not be processed (internal error).');
        //            return;
        //        }
        //
        //        $ffprobe = FFProbe::create([
        //            'timeout' => 120,
        //        ]);
        //
        //        $duration = $ffprobe->format($value->getRealPath())
        //            ->get('duration');
        //
        //        Storage::disk('local')->delete($tempPath);

        if (app()->environment('local')) {
            if (!$process->isSuccessful()) {
                $fail('Cannot check the file');
            }

            $duration = (float) $process->getOutput();

            if ($duration > $durationLimit * 60) {
                $fail('Limit the duration');
            }
        }
    }
}
