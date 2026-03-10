<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RoleAndPermissionSeeder::class,
            VocalSeeder::class,
            AdminSeeder::class,
            WorldSeeder::class,
            SubscriptionPlansSeeder::class,

            // Data Import: Always do these last
            DataImportTableUsers::class,
            DataImportTableMusicengineRoles::class,
            DataImportTableOAuthSocialite::class,
            DataImportTableActivities::class,
            DataImportTableGenres::class,
            DataImportTableBanners::class,
            DataImportTableChannels::class,
            DataImportTableComments::class,
            DataImportTableEmails::class,
            DataImportTableHistories::class,
            DataImportTableLove::class,
            DataImportTableMedia::class,
            DataImportTableMetatags::class,
            DataImportTableNotifications::class,
            DataImportTablePages::class,
            DataImportTablePasswordResets::class,
            DataImportTablePlaylists::class,
            DataImportTablePlaylistSongs::class,
            DataImportTablePosts::class,
            DataImportTableReactions::class,
            DataImportTableSongs::class,
            DataImportTableSongTags::class,
            GenreablesAll::class,
        ]);
    }
}
