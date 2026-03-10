<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class DataImportTable extends Seeder
{
    protected ?string $srcConnection;
    protected ?string $destConnection;
    protected ?string $srcTable;
    protected ?string $destTable;

    // Indexed $src_column_name => $dest_column_name
    protected array $columnMap = [];

    // Indexed $column_name => $value
    // Some keys have dedicated handler methods prefixed 'fill'
    // See fillUuid()
    protected array $extraDestColumns = [];

    protected int $pageSize = 1000;
    protected readonly string $classBasename;

    protected $srcDB;
    protected $destDB;
    protected $destDriver;
    protected $mindDestFK;
    protected $srcFilter;
    protected array $batchQueue = [];
    protected $emptyDest = true;

    public function __construct()
    {
        $this->classBasename = 'DataImportTable';
    }

    ////////////////////////////////////////////////////////////
    // TODO: Implement custom column transform functions
    ////////////////////////////////////////////////////////////

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $start_time = microtime(true);
        $start_time = Carbon::now();

        $this->getConfig();

        $extraColMethods = [];

        $this->srcConnection = trim($this->srcConnection);
        $this->destConnection = trim($this->destConnection);

        $this->srcTable = trim($this->srcTable);
        $this->destTable = trim($this->destTable);

        // Check Connections and tables
        $this->checkTable($this->srcConnection, $this->srcTable);
        $this->checkTable($this->destConnection, $this->destTable);

        if ($this->srcConnection == $this->destConnection && $this->src_table == $this->destTable)
        {
            throw new Exception(get_class($this) . ': Cannot migrate to and from same table.');
        }

        $src = Schema::connection($this->srcConnection);
        $dest = Schema::connection($this->destConnection);

        $this->srcDB = DB::connection($this->srcConnection);
        $this->destDB = DB::connection($this->destConnection);

        $src_col_list = Schema::connection($this->srcConnection)->getColumnListing($this->srcTable);
        $dest_col_list = Schema::connection($this->destConnection)->getColumnListing($this->destTable);

        $src_cols = Schema::connection($this->srcConnection)->getColumns($this->srcTable);
        $dest_cols = Schema::connection($this->destConnection)->getColumns($this->destTable);

        $this->destDriver = $this->destDB->getConfig('driver');

        // If Destination FKs enabled, manage them
        // Otherwise, leave them be
        $this->mindDestFK = !$this->getDestFKEnabled();

        $this->command->info('Minding destination foreign key checks: ' . ($this->mindDestFK ? 'Yes' : 'No'));

        if (count($this->columnMap) < 1)
        {
            throw new Exception(get_class($this) . ': Column map is empty.');
        }

        // Check col maps against actual columns
        foreach ($this->columnMap as $s => $d)
        {
            if (!in_array($s, $src_col_list)) {
                throw new Exception(get_class($this) . ': Mapped column ' . $this->srcTable . '.' . $s . ' does not exist in source connection.');
            }

            if (!in_array($d, $dest_col_list)) {
                throw new Exception(get_class($this) . ': Mapped column ' . $this->destTable . '.' . $d . ' does not exist in destination connection.');
            }

            $srcType = Schema::connection($this->srcConnection)->getColumnType($this->srcTable, $s);
            $destType = Schema::connection($this->destConnection)->getColumnType($this->destTable, $d);

            // Show src->dest column type comparisons
            if ($srcType == $destType) {
                $this->command->info($this->srcConnection . '.' . $this->srcTable . '.' . $s . ' (' . $srcType . ') matches ' . $this->destConnection . '.' . $this->destTable . '.' . $d . ' (' . $destType . ')');
            } else {
                $this->command->warn($this->srcConnection . '.' . $this->srcTable . '.' . $s . ' (' . $srcType . ') not like ' . $this->destConnection . '.' . $this->destTable . '.' . $d . ' (' . $destType . ')');
            }
        }

        // Cache extra column methods for later
        if (count($this->extraDestColumns) > 0) {
            foreach ($this->extraDestColumns as $e => $v) {
                if (!in_array($e, $dest_col_list)) {
                    throw new Exception(get_class($this) . ': Extra column ' . $this->destTable . '.' . $e . ' does not exist in destination connection');
                }

                $colMethod = 'fill' . Str::Studly($e);

                if (method_exists($this, $colMethod)) {
                    $extraColMethods[$e] = $colMethod;
                }

                if (in_array($e, $this->columnMap)) {
                    $this->command->warn($this->destConnection . '.' . $this->destTable . '.' . $e . ' will OVERWRITE ' . $this->destConnection . '.' . $this->destTable . '.' . $d . ' (' . $destType . ') values.');
                } else {
                    $this->command->info('Including extra column ' . $this->destConnection . '.' . $this->destTable . '.' . $e);
                }
            }
        }

        $row_count = $this->srcDB->table($this->srcTable)->count();

        if ($row_count == 0) {
            $this->command->info('Source table ' . $this->srcConnection . '.' . $this->srcTable . ' has no data to migrate.');

            return;
        } else {
            $this->command->info('Migrating ' . $row_count . ' rows from ' . $this->srcConnection . '.' . $this->srcTable . ' to ' . $this->destConnection . '.' . $this->destTable . '.');
        }

        $dest_row_count = $this->destDB->table($this->destTable)->count();

        if ($dest_row_count == 0) {
            $this->command->info('Destination table ' . $this->destConnection . '.' . $this->destTable . ' is empty.');
        } else {
            $this->command->warn('Destroying ' . $dest_row_count . ' rows in ' . $this->destConnection . '.' . $this->destTable . '.');
        }

        try {
            // Disable FK checks
            if ($this->mindDestFK) {
                $this->setDestFKStatus(false);
            }

            // Truncate destination table
            // Always, in case AUTO_INCREMENT is not 0
            // Unless there is a controlled reason not to
            if ($this->emptyDest) {
                $this->destDB->table($this->destTable)->truncate();
            }

            // Process source data

            $this->destDB->transaction(function () use ($dest_col_list, $extraColMethods) {
                $allRows = new Collection();
                $page = 1;

                do {
                    if (is_null($this->srcFilter)) {
                        $chunk = $this->srcDB->table($this->srcTable)->paginate($this->pageSize, ['*'], 'page', $page);
                    } else {
                        $chunk = $this->srcDB->table($this->srcTable)->where($this->srcFilter)->paginate($this->pageSize, ['*'], 'page', $page);
                    }

                    $chunkData = [];

                    foreach ($chunk as $c) {
                        $item = [];

                        foreach ($this->columnMap as $s => $d) {
                            $item[$d] = $c->{$s};
                        }

                        if (count($this->extraDestColumns) > 0) {
                            foreach ($this->extraDestColumns as $e => $v) {
                                if (!in_array($d, $dest_col_list)) {
                                    continue;
                                }

                                if (array_key_exists($e, $extraColMethods)) {
                                    $colMethod = $extraColMethods[$e];
                                    $item[$e] = $this->$colMethod($v);
                                } else {
                                    $item[$e] = $v;
                                }
                            }
                        }
                        // The preInsert method exists in child classes to perform
                        // additional row-level manipulations.
                        if (method_exists($this, 'preInsert') && is_callable([$this, 'preInsert'])) {
                            $this->preInsert($item);
                        }
                        if (count($this->batchQueue) == 0) {
                            $chunkData[] = $item;
                        } else {
                            $chunkData = array_merge($chunkData, $this->batchQueue);
                            $this->batchQueue = [];
                        }
                    }

                    $this->destDB->table($this->destTable)->insert($chunkData);

                    $this->command->info('[' . $this->destTable . '] Page ' . $page . ' completed.');

                    $page++;
                }
                while ($chunk->hasMorePages());
            });

            // Restore FK checks
            if ($this->mindDestFK) {
                $this->setDestFKStatus(true);
            }

            // Finish up
            $dest_row_count = $this->destDB->table($this->destTable)->count();

            $finish_time = Carbon::now();
            $formatted_duration = $finish_time->diff($start_time)->format('%H:%I:%S.%F');

            if ($dest_row_count == 0) {
                $this->command->info('Destination table ' . $this->destConnection . '.' . $this->destTable . ' is empty.  Ran in ' . $formatted_duration . '.');
            } else {
                $this->command->warn('Migrated ' . $dest_row_count . ' rows to ' . $this->destConnection . '.' . $this->destTable . ' in ' . $formatted_duration . '.');
            }

            // Check for callable more() method
            if (method_exists($this, 'runMore') && is_callable([$this, 'runMore'])) {
                $this->command->info('Calling ' . get_class($this) . '::runMore()');

                $this->runMore();
            } else {
                $this->command->info('Callable runMore() not present in ' . get_class($this) . '.');
            }

        } catch (Exception $e) {
            // Restore FK checks
            if ($this->mindDestFK) {
                $this->setDestFKStatus(true);
            }

            throw new Exception(get_class($this) . ': Data migration Failed; ' . $e->getMessage());
        }
    }

    private function getConfig() {
        if (!config()->has('data-import-table')) {
            throw new Exception(get_class($this) . ': General Data Import config missing.');
        }

        $c = new \ReflectionClass($this);
        $me = $c->getShortName();

        $conf_key = preg_replace('/^' . $this->classBasename . '/', '', $me);
        $conf_key = Str::snake($conf_key);

        if (!config()->has('data-import-table.tables.' . $conf_key)) {
            throw new Exception(get_class($this) . ': Data Import config not found.');
        }

        $this->srcConnection = trim(config('data-import-table.defaults.source_connection'));
        $this->destConnection = trim(config('data-import-table.defaults.dest_connection'));
        $this->pageSize = (int) config('data-import-table.defaults.page_size');

        // Override defaults
        if (config()->has('data-import-table.tables.' . $conf_key . '.source_connection')) {
            $this->srcConnection = trim(config('data-import-table.tables.' . $conf_key . '.source_connection'));
        }

        if (config()->has('data-import-table.tables.' . $conf_key . '.dest_connection')) {
            $this->destConnection = trim(config('data-import-table.tables.' . $conf_key . '.dest_connection'));
        }

        if (config()->has('data-import-table.tables.' . $conf_key . '.page_size')) {
            $this->pageSize = trim(config('data-import-table.tables.' . $conf_key . '.page_size'));
        }

        if (!config()->has('data-import-table.tables.' . $conf_key . '.source_table')) {
            throw new Exception(get_class($this) . ': Data Import config source column not found.');
        }
        else {
            $this->srcTable = trim(config('data-import-table.tables.' . $conf_key . '.source_table'));
        }

        if (!config()->has('data-import-table.tables.' . $conf_key . '.dest_table')) {
            throw new Exception(get_class($this) . ': Data Import config destination column not found.');
        }
        else {
            $this->destTable = trim(config('data-import-table.tables.' . $conf_key . '.dest_table'));
        }

        if (!config()->has('data-import-table.tables.' . $conf_key . '.column_map')) {
            throw new Exception(get_class($this) . ': Data Import config column map not found.');
        }
        else {
            $this->columnMap = config('data-import-table.tables.' . $conf_key . '.column_map');
        }

        if (config()->has('data-import-table.tables.' . $conf_key . '.extra_dest_columns')) {
            $this->extraDestColumns = config('data-import-table.tables.' . $conf_key . '.extra_dest_columns');
        }

        if (config()->has('data-import-table.tables.' . $conf_key . '.source_filter')) {
            $f = config('data-import-table.tables.' . $conf_key . '.source_filter');

            if (count($f) == 0) {
                $this->command->warn(get_class($this) . ': Ignoring empty source_filter');
            } else {
                if (!is_array($f)) {
                    throw new Exception(get_class($this) . ': Data Import source_filter must be an array.');
                }

                foreach ($f as $k => $v) {
                    if (!is_array($v)) {
                        throw new Exception(get_class($this) . ': Data Import source_filter[' . $k . '] must be an array.');
                    }

                    if (count($v) < 2) {
                        throw new Exception(get_class($this) . ': Data Import source_filter[' . $k . '] not suitable arguments for where().');
                    }
                }

                $this->srcFilter = $f;
            }
        }

        if (config()->has('data-import-table.tables.' . $conf_key . '.empty_destination')) {
            $this->emptyDest = (bool) config('data-import-table.tables.' . $conf_key . '.empty_destination');
        }

    }

    private function checkConnection(string $conn) {
        $conn = trim($conn);

        if ($conn == '') {
            throw new Exception(get_class($this) . ': Database connection name cannot be empty.');
        }

        if (!config()->has('database.connections.' . $conn)) {
            throw new Exception(get_class($this) . ': Unknown database connection "' . $conn . '".');
        }

        try {
            DB::connection($conn)->getPdo();
        }
        catch (QueryException $e) {
            throw new Exception(get_class($this) . ': Could not connect to database "' . $conn . '"; ' . $e->getMessage());
        }
        catch (\Exception $e) {
            throw new Exception(get_class($this) . ': Unknown error connecting to database "' . $conn . '"; ' . $e->getMessage());
        }
    }

    private function checkTable(string $conn, string $table) {
        $table = trim($table);

        if ($table == '') {
            throw new Exception(get_class($this) . ': table name cannot be empty.');
        }

        $conn = trim($conn);
        $this->checkConnection($conn);

        if (!Schema::connection($conn)->hasTable($table)) {
            throw new Exception(get_class($this) . ': Connection "' . $conn . '" unknown table "' . $table . '".');
        }
    }

    // returns bool: true if enabled
    private function getDestFKEnabled() {
        if ($this->destDriver == 'mysql' || $this->destDriver == 'mariadb') {
            $fks = $this->destDB->selectOne("SHOW VARIABLES LIKE 'foreign_key_checks'");

            return (is_object($fks) && $fks->Value == '1');
        } else if ($this->destDriver == 'sqlite') {
            $dconf = config('database.connections' . $this->destConnection);

            return (array_key_exists('foreign_key_constraints', $dconf) && $dconf['foreign_key_constrainsts'] == true);
        } else if ($this->destDriver == 'pgsql') {
            $fks = $this->destDB->selectOne('SHOW session_replication_role;')->session_replication_role;

            return ($fks == 'origin');
        } else {
            throw new Exception(get_class($this) . ': Unsupported DB type "' . $this->destDriver . '" for destination "' . $this->destConnection . '.');
        }
    }

    private function setDestFKStatus(bool $s) {
        if ($s) {
            $this->destDB->getSchemaBuilder()->enableForeignKeyConstraints();
        } else {
            $this->destDB->getSchemaBuilder()->disableForeignKeyConstraints();
        }
    }

    private function fillUuid (bool $ordered) {
        if ($ordered) {
            return Str::orderedUuid();
        } else {
            return Str::uuid();
        }
    }

}
