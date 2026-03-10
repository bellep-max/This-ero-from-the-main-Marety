<?php

use App\Models\User;
use App\Models\Vocal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('songs')) {
            Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Vocal::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            if (config('database.default') !== 'sqlite') {
                $table->fullText('title');
            }
            $table->boolean('mp3')->default(0)->index();
            $table->boolean('flac')->default(0)->index();
            $table->boolean('wav')->default(0)->index();
            $table->boolean('hd')->nullable()->default(0)->index();
            $table->boolean('hls')->default(0)->index();
            $table->decimal('price', 10)->nullable()->default(0.00);
            $table->boolean('selling')->default(0)->index();
            $table->boolean('explicit')->default(0)->index();
            $table->string('mood', 50)->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('access')->nullable();
            $table->smallInteger('duration')->nullable();
            $table->string('artistIds', 50)->nullable();
            $table->integer('loves')->default(0);
            $table->integer('collectors')->default(0)->index();
            $table->integer('plays')->default(0);
            $table->integer('referral_plays');
            $table->date('released_at')->nullable();
            $table->string('copyright')->nullable();
            $table->boolean('allow_download')->default(1)->index();
            $table->mediumInteger('download_count')->default(0);
            $table->boolean('allow_comments')->default(1);
            $table->mediumInteger('comment_count')->default(0);
            $table->boolean('is_visible')->default(1)->index();
            $table->boolean('approved')->default(0)->index();
            $table->boolean('pending')->default(0)->index();
            $table->boolean('is_adventure')->nullable()->default(0);
            $table->boolean('is_patron')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
