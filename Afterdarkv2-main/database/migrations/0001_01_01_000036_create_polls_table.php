<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('polls')) {
            Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('object_type', 50)->nullable()->index();
            $table->unsignedInteger('object_id')->nullable()->index();
            $table->string('title');
            $table->text('body')->nullable();
            $table->mediumInteger('votes')->default(0);
            $table->boolean('multiple')->nullable()->default(0);
            $table->text('answer');
            $table->boolean('is_visible')->nullable()->default(1);
            $table->timestamp('started_at')->nullable()->index('started_at');
            $table->timestamp('ended_at')->nullable()->index('ended_at');
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
