<?php

namespace App\Http\Controllers\Backend\Encore\Scaffold;

use Exception;
use Illuminate\Support\Str;

class ModelCreator
{
    protected string $tableName;

    protected string $name;

    protected mixed $files;

    public function __construct(string $tableName, string $name, $files = null)
    {
        $this->tableName = $tableName;

        $this->name = $name;

        $this->files = $files ?: app('files');
    }

    public function create(string $keyName = 'id', bool $timestamps = true, bool $softDeletes = false)
    {
        $path = $this->getpath($this->name);

        if ($this->files->exists($path)) {
            throw new Exception("Model [$this->name] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceClass($stub, $this->name)
            ->replaceNamespace($stub, $this->name)
            ->replaceSoftDeletes($stub, $softDeletes)
            ->replaceTable($stub, $this->name)
            ->replaceTimestamp($stub, $timestamps)
            ->replacePrimaryKey($stub, $keyName)
            ->replaceSpace($stub);

        $this->files->put($path, $stub);

        return $path;
    }

    public function getPath(string $name)
    {
        $segments = explode('\\', $name);

        array_shift($segments);

        return app_path(implode('/', $segments)) . '.php';
    }

    protected function getNamespace(string $name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function replaceClass(string &$stub, string $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        $stub = str_replace('DummyClass', $class, $stub);

        return $this;
    }

    protected function replaceNamespace(string &$stub, string $name)
    {
        $stub = str_replace(
            'DummyNamespace',
            $this->getNamespace($name),
            $stub
        );

        return $this;
    }

    protected function replaceSoftDeletes(string &$stub, bool $softDeletes)
    {
        $import = $use = '';

        if ($softDeletes) {
            $import = "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n";
            $use = "use SoftDeletes;\n";
        }

        $stub = str_replace(['DummyImportSoftDeletesTrait', 'DummyUseSoftDeletesTrait'], [$import, $use], $stub);

        return $this;
    }

    protected function replacePrimaryKey(string &$stub, string $keyName)
    {
        $modelKey = $keyName == 'id' ? '' : "protected \$primaryKey = '$keyName';\n";

        $stub = str_replace('DummyModelKey', $modelKey, $stub);

        return $this;
    }

    protected function replaceTable(string &$stub, string $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        $table = Str::plural(strtolower($class)) !== $this->tableName ? "protected \$table = '$this->tableName';\n" : '';

        $stub = str_replace('DummyModelTable', $table, $stub);

        return $this;
    }

    protected function replaceTimestamp(string &$stub, bool $timestamps)
    {
        $useTimestamps = $timestamps ? '' : "public \$timestamps = false;\n";

        $stub = str_replace('DummyTimestamp', $useTimestamps, $stub);

        return $this;
    }

    public function replaceSpace(string $stub)
    {
        return str_replace(["\n\n\n", "\n    \n"], ["\n\n", ''], $stub);
    }

    public function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }
}
