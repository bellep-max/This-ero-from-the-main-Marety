<?php

use App\Constants\DefaultConstants;
use App\Models\Adventure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('adventure_properties')) {
            Schema::create('adventure_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Adventure::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_visible')->default(DefaultConstants::TRUE);
            $table->boolean('allow_comments')->default(DefaultConstants::TRUE);
            $table->boolean('approved')->default(DefaultConstants::TRUE);
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('adventure_properties');
    }
};
