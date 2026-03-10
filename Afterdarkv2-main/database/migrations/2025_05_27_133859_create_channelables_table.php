<?php

use App\Models\Channel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('channelables')) {
            Schema::create('channelables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->morphs('channelable');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('channelables');
    }
};
