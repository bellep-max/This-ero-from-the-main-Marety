<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-22
 * Time: 18:13.
 */

namespace App\Http\Controllers\Backend\Encore;

use Exception;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Str;
use Throwable;

class Scheduling
{
    /**
     * @var string out put file for command.
     */
    protected $sendOutputTo;

    /**
     * Get all events in console kernel.
     */
    protected function getKernelEvents(): array
    {
        app()->make('Illuminate\Contracts\Console\Kernel');

        return app()->make('Illuminate\Console\Scheduling\Schedule')->events();
    }

    /**
     * Get all formatted tasks.
     *
     * @throws Exception
     */
    public function getTasks(): array
    {
        $tasks = [];

        foreach ($this->getKernelEvents() as $event) {
            $tasks[] = [
                'task' => $this->formatTask($event),
                'expression' => $event->expression,
                'nextRunDate' => $event->nextRunDate()->format('Y-m-d H:i:s'),
                'description' => $event->description,
                'readable' => CronSchedule::fromCronString($event->expression)->asNaturalLanguage(),
            ];
        }

        return $tasks;
    }

    /**
     * Format a giving task.
     */
    protected function formatTask($event): array
    {
        if ($event instanceof CallbackEvent) {
            return [
                'type' => 'closure',
                'name' => 'Closure',
            ];
        }
        if (Str::contains($event->command, '\'artisan\'')) {
            $exploded = explode(' ', $event->command);

            return [
                'type' => 'artisan',
                'name' => 'artisan ' . implode(' ', array_slice($exploded, 2)),
            ];
        }

        return [
            'type' => 'command',
            'name' => $event->command,
        ];
    }

    /**
     * Run specific task.
     *
     * @throws Throwable
     */
    public function runTask(int $id): string
    {
        set_time_limit(0);
        /** @var Event $event */
        $event = $this->getKernelEvents()[$id - 1];
        $event->sendOutputTo($this->getOutputTo());
        $event->run(app());

        return $this->readOutput();
    }

    protected function getOutputTo(): string
    {
        if (!$this->sendOutputTo) {
            $this->sendOutputTo = storage_path('app/task-schedule.output');
        }

        return $this->sendOutputTo;
    }

    /**
     * Read output info from output file.
     */
    protected function readOutput(): string
    {
        return file_get_contents($this->getOutputTo());
    }
}
