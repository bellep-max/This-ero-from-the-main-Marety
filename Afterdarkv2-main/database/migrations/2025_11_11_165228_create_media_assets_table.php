<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('media_assets')) {
            Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('parent_id')->nullable()->constrained('media_assets')->cascadeOnUpdate()->nullOnDelete();
            $table->morphs('assetable');
            $table->enum('type', ['master', 'rendition'])->default('master');
            $table->string('format', 20);
            $table->string('quality', 20)->nullable();
            $table->string('path');
            $table->string('filename');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('bitrate')->nullable();
            $table->unsignedInteger('sample_rate')->nullable();
            $table->unsignedSmallInteger('duration')->nullable();
            $table->string('codec', 50)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['assetable_type', 'assetable_id', 'type']);
            $table->index(['parent_id', 'format']);
            $table->index(['status', 'created_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
