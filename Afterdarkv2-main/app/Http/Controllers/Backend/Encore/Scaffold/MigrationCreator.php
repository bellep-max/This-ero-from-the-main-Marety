<?php

namespace App\Http\Controllers\Backend\Encore\Scaffold;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;

class MigrationCreator extends BaseMigrationCreator
{
    protected string $bluePrint = '';

    /**
     * Create a new model.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  null  $table
     * @param  bool|true  $create
     *
     * @throws FileNotFoundException
     */
    public function create($name, $path, $table = null, $create = true): string
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        $path = $this->getPath($name, $path);

        $stub = $this->files->get(__DIR__ . '/stubs/create.stub');

        $this->files->put($path, $this->populateStub($name, $stub, $table));

        $this->firePostCreateHooks($table);

        return $path;
    }

    /**
     * Populate stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string  $table
     */
    protected function populateStub($name, $stub, $table): string|array
    {
        return str_replace(
            ['DummyClass', 'DummyTable', 'DummyStructure'],
            [$this->getClassName($name), $table, $this->bluePrint],
            $stub
        );
    }

    /**
     * Build the table blueprint.
     *
     * @return $this
     *
     * @throws Exception
     */
    public function buildBluePrint(array $fields = [], string $keyName = 'id', bool $useTimestamps = true, bool $softDeletes = false): static
    {
        $fields = array_filter($fields, function ($field) {
            return !empty($field['name']);
        });

        if (empty($fields)) {
            throw new Exception('Table fields can\'t be empty');
        }

        $rows[] = "\$table->increments('$keyName');\n";

        foreach ($fields as $field) {
            $column = "\$table->{$field['type']}('{$field['name']}')";

            if ($field['key']) {
                $column .= "->{$field['key']}()";
            }

            if (isset($field['default']) && $field['default']) {
                $column .= "->default('{$field['default']}')";
            }

            if (isset($field['comment']) && $field['comment']) {
                $column .= "->comment('{$field['comment']}')";
            }

            if (array_get($field, 'nullable') == 'on') {
                $column .= '->nullable()';
            }

            $rows[] = $column . ";\n";
        }

        if ($useTimestamps) {
            $rows[] = "\$table->timestamps();\n";
        }

        if ($softDeletes) {
            $rows[] = "\$table->softDeletes();\n";
        }

        $this->bluePrint = trim(implode(str_repeat(' ', 12), $rows), "\n");

        return $this;
    }
}
