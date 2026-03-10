<?php

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use App\Services\MigrationService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        if (!Schema::hasTable('artist_song')) {
            Schema::create('artist_song', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Artist::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(Song::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            });
        }
            if (!$isSqlite && MigrationService::createArtistModelRelations(Song::class, 'artists')) {
                Schema::table('songs', function (Blueprint $table) {
                    $table->dropColumn('artistIds');
                });
        }

        if (!Schema::hasTable('artist_album')) {
            Schema::create('artist_album', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Artist::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(Album::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            });
        }
            if (!$isSqlite && MigrationService::createArtistModelRelations(Album::class, 'artists')) {
                Schema::table('albums', function (Blueprint $table) {
                    $table->dropColumn('artistIds');
                });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('artist_song');

        Schema::table('songs', function (Blueprint $table) {
            $table->string('artistIds', 50)->nullable();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->string('artistIds', 50)->nullable();
        });
        }
};
