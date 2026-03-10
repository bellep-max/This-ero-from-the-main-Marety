<?php

/**
 * Copyright (c) 2015 Jens Segers
 * The MIT License (MIT).
 *
 * @laravel-admin-extensions/log-viewer
 */

namespace App\Http\Controllers\Backend\Encore;

use Exception;

/**
 * Class LogViewer.
 */
class LogViewer
{
    /**
     * The log file name.
     */
    public string $file;

    /**
     * The path of log file.
     */
    protected string $filePath;

    /**
     * Start and end offset in current page.
     */
    protected array $pageOffset = [];

    public static array $levelColors = [
        'EMERGENCY' => 'dark',
        'ALERT' => 'warning',
        'CRITICAL' => 'maroon',
        'ERROR' => 'danger',
        'WARNING' => 'orange',
        'NOTICE' => 'light-blue',
        'INFO' => 'aqua',
        'DEBUG' => 'success',
    ];

    /**
     * LogViewer constructor.
     *
     * @param  null  $file
     *
     * @throws Exception
     */
    public function __construct($file = null)
    {
        if (is_null($file)) {
            $file = $this->getLastModifiedLog();
        }
        $this->file = $file;
        $this->getFilePath();
    }

    /**
     * Get file path by giving log file name.
     *
     *
     * @throws Exception
     */
    public function getFilePath(): string
    {
        if (!$this->filePath) {
            $path = sprintf(storage_path('logs/%s'), $this->file);
            if (!file_exists($path)) {
                throw new Exception('log not exists!');
            }
            $this->filePath = $path;
        }

        return $this->filePath;
    }

    /**
     * Get size of log file.
     */
    public function getFilesize(): int
    {
        return filesize($this->filePath);
    }

    /**
     * Get log file list in storage.
     */
    public function getLogFiles(int $count = 20): array
    {
        $files = glob(storage_path('logs/*'));
        $files = array_combine($files, array_map('filemtime', $files));
        arsort($files);
        $files = array_map('basename', array_keys($files));

        return array_slice($files, 0, $count);
    }

    /**
     * Get the last modified log file.
     */
    public function getLastModifiedLog(): string
    {
        $logs = $this->getLogFiles();

        return current($logs);
    }

    /**
     * Get previous page url.
     */
    public function getPrevPageUrl(): bool|string
    {
        if ($this->pageOffset['end'] >= $this->getFilesize() - 1) {
            return false;
        }

        return route('backend.log-viewer-file', [
            'file' => $this->file, 'offset' => $this->pageOffset['end'],
        ]);
    }

    /**
     * Get Next page url.
     */
    public function getNextPageUrl(): bool|string
    {
        if ($this->pageOffset['start'] == 0) {
            return false;
        }

        return route('backend.log-viewer-file', [
            'file' => $this->file, 'offset' => -$this->pageOffset['start'],
        ]);
    }

    /**
     * Fetch logs by giving offset.
     *
     *
     * @see http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     */
    public function fetch(int $seek = 0, int $lines = 20, int $buffer = 4096): array
    {
        $f = fopen($this->filePath, 'rb');
        if ($seek) {
            fseek($f, abs($seek));
        } else {
            fseek($f, 0, SEEK_END);
        }
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }
        fseek($f, -1, SEEK_CUR);
        // 从前往后读,上一页
        // Start reading
        if ($seek > 0) {
            $output = '';
            $this->pageOffset['start'] = ftell($f);
            while (!feof($f) && $lines >= 0) {
                $output = $output . ($chunk = fread($f, $buffer));
                $lines -= substr_count($chunk, "\n[20");
            }
            $this->pageOffset['end'] = ftell($f);
            while ($lines++ < 0) {
                $strpos = strrpos($output, "\n[20") + 1;
                $_ = mb_strlen($output, '8bit') - $strpos;
                $output = substr($output, 0, $strpos);
                $this->pageOffset['end'] -= $_;
            }
            // 从后往前读,下一页
        } else {
            $output = '';
            $this->pageOffset['end'] = ftell($f);
            while (ftell($f) > 0 && $lines >= 0) {
                $offset = min(ftell($f), $buffer);
                fseek($f, -$offset, SEEK_CUR);
                $output = ($chunk = fread($f, $offset)) . $output;
                fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
                $lines -= substr_count($chunk, "\n[20");
            }
            $this->pageOffset['start'] = ftell($f);
            while ($lines++ < 0) {
                $strpos = strpos($output, "\n[20") + 1;
                $output = substr($output, $strpos);
                $this->pageOffset['start'] += $strpos;
            }
        }
        fclose($f);

        return $this->parseLog($output);
    }

    /**
     * Get tail logs in log file.
     */
    public function tail(int $seek): array
    {
        // Open the file
        $f = fopen($this->filePath, 'rb');
        if (!$seek) {
            // Jump to last character
            fseek($f, -1, SEEK_END);
        } else {
            fseek($f, abs($seek));
        }
        $output = '';
        while (!feof($f)) {
            $output .= fread($f, 4096);
        }
        $pos = ftell($f);
        fclose($f);
        $logs = [];
        foreach ($this->parseLog(trim($output)) as $log) {
            $logs[] = $this->renderTableRow($log);
        }

        return [$pos, $logs];
    }

    /**
     * Render table row.
     */
    protected function renderTableRow($log): string
    {
        $color = self::$levelColors[$log['level']] ?? 'black';
        $index = uniqid();
        $button = '';
        if (!empty($log['trace'])) {
            $button = "<a class=\"btn btn-primary btn-xs\" data-toggle=\"collapse\" data-target=\".trace-$index\"><i class=\"fa fa-info\"></i>&nbsp;&nbsp;Exception</a>";
        }
        $trace = '';
        if (!empty($log['trace'])) {
            $trace = "<tr class=\"collapse trace-$index\">
    <td colspan=\"5\"><div style=\"white-space: pre-wrap;background: #333;color: #fff; padding: 10px;\">{$log['trace']}</div></td>
</tr>";
        }

        return <<<TPL
<tr style="background-color: rgb(255, 255, 213);">
    <td><span class="label bg-$color">{$log['level']}</span></td>
    <td><strong>{$log['env']}</strong></td>
    <td  style="width:150px;">{$log['time']}</td>
    <td><code>{$log['info']}</code></td>
    <td>$button</td>
</tr>
$trace
TPL;
    }

    /**
     * Parse raw log text to array.
     */
    protected function parseLog($raw): array
    {
        $logs = preg_split('/\[(\d{4}(?:-\d{2}){2} \d{2}(?::\d{2}){2})\] (\w+)\.(\w+):((?:(?!{"exception").)*)?/', trim($raw), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($logs as $index => $log) {
            if (preg_match('/^\d{4}/', $log)) {
                break;
            } else {
                unset($logs[$index]);
            }
        }
        if (empty($logs)) {
            return [];
        }
        $parsed = [];
        foreach (array_chunk($logs, 5) as $log) {
            $parsed[] = [
                'time' => $log[0] ?? '',
                'env' => $log[1] ?? '',
                'level' => $log[2] ?? '',
                'info' => $log[3] ?? '',
                'trace' => trim($log[4] ?? ''),
            ];
        }
        unset($logs);
        rsort($parsed);

        return $parsed;
    }
}
