<?php

use App\Models\Activity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('actionables')) {
            Schema::create('actionables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Activity::class, 'activity_id');
            $table->morphs('actionable');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('actionables');
    }
};
