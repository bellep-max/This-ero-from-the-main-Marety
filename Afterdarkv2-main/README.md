## AfterDarkAudio (Vue.js version)

This is an audio content platform for creators and listeners.

# Installation

Create local primary database schema and data import source schema.  Import contents of provided `.sql` file to the source schema.

Clone repository and `cd` into it.

## Laravel Setup

Copy `.env.example` to `.env`.  Populate the necessary values, including the `DATA_IMPORT_*` items near the end.

If you are using Docker, run:

    docker-compose up -d
    docker exec -it erocast-php bash

Run

    composer install
    php artisan storage:link
    php artisan migrate --seed

If updating an existing setup, you may also need to do

    php artisan optimize:clear

Seeding now includes the DataImportTable descendant classes (see `DATAIMPORT.md`).  The current ordered list of DataImportTable seeders is:

```
DataImportTableUsers
DataImportTableMusicengineRoles
DataImportTableOAuthSocialite
DataImportTableActivities
DataImportTableGenres
DataImportTableBanners
DataImportTableChannels
DataImportTableComments
DataImportTableEmails
DataImportTableHistories
DataImportTableLove
DataImportTableMedia
DataImportTableMetatags
DataImportTableNotifications
DataImportTablePages
DataImportTablePasswordResets
DataImportTablePlaylists
DataImportTablePlaylistSongs
DataImportTablePosts
DataImportTableReactions
DataImportTableSongs
DataImportTableSongTags
GenreablesAll
```

Previously, these were executed individually:

Running them can be simplified by placing the list in file at the top of the repo and doing the following:

    for F in $(cat [seed list file]); do echo "@ ${F}";php artisan db:seed --class=${F}; done;

**NOTE**: running `php artisan migrate:fresh` will require running the data import seeders again.

## Frontend Setup

To install and build the frontend, run

    npm i
    npm run build

