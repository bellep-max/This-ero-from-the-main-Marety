<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('playlists')) {
            Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('collaboration')->default(0)->index('collaborate');
            $table->string('mood', 50)->nullable()->index();
            $table->string('title');
            $table->string('description')->nullable();
            $table->mediumInteger('loves')->default(0);
            $table->boolean('allow_comments')->default(1);
            $table->smallInteger('comment_count')->default(0);
            $table->boolean('is_visible')->default(1);
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
