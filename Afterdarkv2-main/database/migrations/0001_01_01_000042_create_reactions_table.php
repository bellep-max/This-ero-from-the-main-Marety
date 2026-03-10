<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reactions')) {
            Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('reactionable_id')->nullable();
            $table->string('reactionable_type', 50)->nullable();
            $table->string('type', 20)->nullable()->index('reaction_type');
            $table->timestamps();
            $table->unique(['user_id', 'reactionable_id', 'reactionable_type'], 'REACTION_UNIQUE');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
