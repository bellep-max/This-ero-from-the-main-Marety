<?php

use App\Enums\AdventureSongTypeEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('adventures')) {
            Schema::create('adventures', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->boolean('mp3')->default(0)->index();
            $table->boolean('hd')->nullable()->default(0)->index();
            $table->boolean('hls')->default(0)->index();
            $table->foreignId('parent_id')->nullable()->constrained('adventures')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('type')->default(AdventureSongTypeEnum::Heading->value);
            $table->unsignedTinyInteger('order')->nullable();
            $table->string('file_url');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration');
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('adventures');
    }
};
