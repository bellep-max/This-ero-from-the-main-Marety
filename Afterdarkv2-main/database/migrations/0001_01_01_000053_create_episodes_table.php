<?php

use App\Models\Podcast;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('episodes')) {
            Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->mediumInteger('season')->nullable()->index();
            $table->mediumInteger('number')->nullable()->index();
            $table->boolean('type')->default(1);
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('hls')->nullable();
            $table->boolean('mp3')->nullable();
            $table->foreignIdFor(Podcast::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title')->default('');
            $table->text('description')->nullable();
            $table->string('access')->nullable();
            $table->boolean('explicit')->default(0);
            $table->string('stream_url')->nullable();
            $table->boolean('allow_comments')->default(1)->index();
            $table->integer('comment_count')->default(0);
            $table->boolean('allow_download')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('loves')->default(0);
            $table->integer('play_count')->default(0)->index();
            $table->integer('failed_count')->default(0)->index();
            $table->mediumInteger('duration')->default(0);
            $table->boolean('is_visible')->default(1);
            $table->boolean('approved')->default(1)->index();
            $table->boolean('pending')->default(0)->index();
            $table->timestamps();

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'description']);
            }
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
