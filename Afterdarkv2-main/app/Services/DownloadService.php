<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadService
{
    public array $properties = [
        'media' => '',
        'type' => '',
        'size' => '',
        'resume' => '',
        'max_speed' => '',
    ];

    public string|array|int $range = 0;

    public function __construct($media, $name = '', $resume = 0, $max_speed = 0)
    {
        ob_start();

        $this->properties = [
            'media' => $media,
            'name' => $name,
            'type' => 'application/force-download',
            'size' => $media->size,
            'resume' => $resume,
            'max_speed' => $max_speed,
        ];

        $this->properties['type'] = $this->properties['media']->mime_type;

        if ($this->properties['resume']) {
            if (isset($_SERVER['HTTP_RANGE'])) {
                $this->range = $_SERVER['HTTP_RANGE'];
                $this->range = str_replace('bytes=', '', $this->range);
                $this->range = str_replace('-', '', $this->range);
            } else {
                $this->range = 0;
            }

            if ($this->range > $this->properties['size']) {
                $this->range = 0;
            }
        } else {
            $this->range = 0;
        }
    }

    public function downloadFile(): void
    {
        if ($this->range) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 206 Partial Content');
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $this->properties['type']);
        header('Content-Disposition: attachment; filename="' . $this->properties['name'] . '"');
        header('Content-Transfer-Encoding: binary');

        if ($this->properties['resume']) {
            header('Accept-Ranges: bytes');
        }

        if ($this->range) {
            header("Content-Range: bytes $this->range-" . ($this->properties['size'] - 1) . '/' . $this->properties['size']);
            header('Content-Length: ' . ($this->properties['size'] - $this->range));
        } else {
            header('Content-Length: ' . $this->properties['size']);
        }

        header('Connection: close');

        @ini_set('max_execution_time', 0);
        @set_time_limit();

        if (config('filesystems.disks')[$this->properties['media']->disk]['driver'] == 'local') {
            $this->downloadThrottle($this->properties['media']->getPath(), $this->range);
        } else {
            $filePath = storage_path('app/public/' . Str::random(32));
            $this->downloadUrlToFile($this->properties['media']->getUrl(), $filePath);
            $this->downloadThrottle($filePath, $this->range, true);
        }
    }

    public function downloadThrottle($filePath, $range = 0, $shouldRemoveFile = false): bool
    {
        @ob_end_clean();

        if (($speed = $this->properties['max_speed']) > 0) {
            $sleep_time = (8 / $speed) * 1e6;
        } else {
            $sleep_time = 0;
        }

        $handle = fopen($filePath, 'rb');
        fseek($handle, $range);

        if ($handle === false) {
            return false;
        }

        while (!feof($handle)) {
            echo fread($handle, 8192);
            ob_flush();
            flush();

            if ($sleep_time) {
                usleep($sleep_time);
            }
        }

        fclose($handle);

        if ($shouldRemoveFile) {
            @unlink($filePath);
        }

        return true;
    }

    public function downloadUrlToFile($url, $outFileName): void
    {
        if (is_file($url)) {
            copy($url, $outFileName);
        } else {
            $options = [
                CURLOPT_FILE => fopen($outFileName, 'w'),
                CURLOPT_TIMEOUT => 28800, // set this to 8 hours so we dont timeout on big files
                CURLOPT_URL => $url,
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    public function downloadTrack(Model $model, bool $hd = false): string
    {
        if (!$model->mp3) {
            abort(404);
        }

        if ($hd) {
            $mediaCollectionName = 'hd_audio';
        } else {
            $mediaCollectionName = 'audio';
        }

        // todo fix this
        //        if (!auth()->user()->hasAnyRole(RoleEnum::getAdminRoles()) || !Group::getValue($allowDownloadOption)) {
        //            abort(403);
        //        }

        if (!$media = $model->getFirstMedia($mediaCollectionName)) {
            abort(404, 'Audio file not found for the requested quality.');
        }

        if (!Storage::disk('wasabi')->exists($media->getPath())) {
            abort(404);
        }

        return Storage::disk('wasabi')
            ->temporaryUrl($media->getPath(), now()->addMinutes(5), [
                'ResponseContentType' => $media->mime_type,
                'ResponseContentDisposition' => 'attachment; filename="' . $model->title . '.mp3"',
            ]);
    }
}
