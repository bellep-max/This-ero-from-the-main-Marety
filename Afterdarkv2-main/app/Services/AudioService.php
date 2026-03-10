<?php

namespace App\Services;

use Exception;
use Symfony\Component\Process\Process;

class AudioService
{
    public static function checkIfFFMPEGInstalled(): bool
    {
        try {
            $process = new Process(['ffmpeg', '-version']);
            $process->run();

            return $process->isSuccessful();
        } catch (Exception $e) {
            return false;
        }
    }
}
