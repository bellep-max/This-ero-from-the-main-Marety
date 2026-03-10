<?php

namespace App\Http\Controllers\Backend\Encore\Scaffold;

use Exception;
use Illuminate\Filesystem\Filesystem;

class ControllerCreator
{
    /**
     * Controller full name.
     */
    protected string $name;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected mixed $files;

    /**
     * ControllerCreator constructor.
     *
     * @param  string  $name
     * @param  null  $files
     */
    public function __construct($name, $files = null)
    {
        $this->name = $name;

        $this->files = $files ?: app('files');
    }

    /**
     * Create a controller.
     *
     * @param  string  $model
     *
     * @throws Exception
     */
    public function create($model): string
    {
        $path = $this->getpath($this->name);

        if ($this->files->exists($path)) {
            throw new Exception("Controller [$this->name] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $this->files->put($path, $this->replace($stub, $this->name, $model));

        return $path;
    }

    /**
     * @param  string  $stub
     * @param  string  $name
     * @param  string  $model
     * @return string
     */
    protected function replace($stub, $name, $model)
    {
        $stub = $this->replaceClass($stub, $name);

        return str_replace(
            ['DummyModelNamespace', 'DummyModel'],
            [$model, class_basename($model)],
            $stub
        );
    }

    /**
     * Get controller namespace from giving name.
     *
     * @param  string  $name
     */
    protected function getNamespace($name): string
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     */
    protected function replaceClass(string $stub, string $name): string
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace(['DummyClass', 'DummyNamespace'], [$class, $this->getNamespace($name)], $stub);
    }

    /**
     * Get file path from giving controller name.
     */
    public function getPath($name): string
    {
        $segments = explode('\\', $name);

        array_shift($segments);

        return app_path(implode('/', $segments)) . '.php';
    }

    /**
     * Get stub file path.
     */
    public function getStub(): string
    {
        return __DIR__ . '/stubs/controller.stub';
    }
}
