<?php

use App\Models\ChildSong;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('adventure_children_finals')) {
            Schema::create('adventure_children_finals', function (Blueprint $table) {
            $table->id();
            $table->boolean('mp3')->default(0)->index();
            $table->boolean('hd')->nullable()->default(0)->index();
            $table->boolean('hls')->default(0)->index();
            $table->foreignIdFor(ChildSong::class, 'adventure_children_id')->nullable()->constrained('adventure_children')->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('duration')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('approved')->default(0)->index();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('wav')->default(0)->index();
            $table->boolean('pending')->default(0)->index();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('adventure_children_finals');
    }
};
