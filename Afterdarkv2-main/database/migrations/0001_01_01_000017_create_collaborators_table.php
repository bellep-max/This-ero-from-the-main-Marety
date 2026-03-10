<?php

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('collaborators')) {
            Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Playlist::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'friend_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('approved')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'playlist_id', 'friend_id'], 'unique');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
