# Data Import

Data import from the legacy database is implemented via

    Database\Seeders\DataImportTable

and its subclasses.

Data is selected from a source connection and saved to a distinct destination
connection.  For maximum efficiency, both should be local to the Laravel workspace.

## Resources

### .env entries

These serve as default values:

* `DATA_IMPORT_SRC_CONN`: Name of the database connection to be used as the data source.
* `DATA_IMPORT_DEST_CONN`: Name of the database connection that will receive data.
* `DATA_IMPORT_PAGE_SIZE`: Number of items per page to import.

Each import class may override these by declaring class variables:

* `$srcConnection`
* `$destConnection`
* `$pageSize`

### Database Config

Both source and destination database connections must be properly configured in
`config/database.php`.  No defaults are provided.

Source is a "staging" schema that has been populated with data from the live site.

Destination is the primary connection the workspace instance uses.

### Data Import Config

Configuration for data import is in `config/data-import-table.php`.

It consists of two indexes; 'defaults' reads `.env` enteries, while `tables` contains data
corresponding to a specific child class of `DataImportTable`.  The seeders use their own
class name to determine the tables index: remove 'DataImportTable', then convert the remainder
to snake_case.

These items may exist each `tables` entry:

* `source_table`: Table name in the source connection to select from.
* `dest_table`: Table name in the destination connection to populate.
* `empty_destination`: Whether the seeder should trundate the destination table (defaults to true).
* `page_size` (optional) : Restricts seed pagination to this many rows (overrides `DATA_IMPORT_PAGE_SIZE` for wide tables).
* `column_map`: Array of column names to migrate; keys are `source` columns, values are `destination` columns.
* `extra_dest_columns` (optional): Array of additional `destination` columns; default values keyed by column name.


### Seeder Behavior

Checks that both connections are valid.

Checks that source & destination connections, tables are different.

Checks that the `column_map` key (source) and value (destination) columns exist in their respective tables.

Reports/warns whether source and destination columns types are identical.

Reports whether source table is empty (and bails), or how many rows it contains.

Reports whether destination table is empty, or how many rows will be destroyed.

Checks whether there is an active transaction at the destination; if not, begins one.

Checks whether to **truncate** the destination table and does so.

If destination connection supports foreign keys, disables foreign key checks.

Iterates over paged data fetched from source, inserting into destination.

Each record will cause calls to any `fill*()` methods presdent, as well as `preInsert()`.

Once iteratiuon is finished, restores foreign key checks at destination if necessary.

Reports new row count of destination table and elapsed time.

Calls `runMore()` method if one exists.  Any post-import further data manipulation is done here.

#### Special Behavior

Members of `extra_dest_columms` may trigger calls of special methods to populate their values.

The column name is converted to StudlyCase; if a method `fill${StudlyColumn}` exists in the seeder, it
will be called and be passed the column value as arguemnts.  See `fillUuid()` in the main class.

If present, the `preInsert()` method can perform custom per-row data preparation.

The base class' `$batchQueue` property can be populated in `preInsert()` to
handle poorly normalized data; see `database/seeders/DataImportTableSongGenreables.php`
and `database/seeders/DataImportTableSongTaggables.php`.

### Batch Calling

Data sent to a polymorphic or otherwise shared table requires a wrapper class
to invoke the individual seeders.  The wrapper class handles table truncation
in place of the individual seeders (which should have their own `empty_destination`
set to `false`.

See `database/seeders/GenreablesAll.php`.



