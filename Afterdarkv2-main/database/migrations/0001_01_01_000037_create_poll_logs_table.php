<?php

use App\Models\Poll;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('poll_logs')) {
            Schema::create('poll_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Poll::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('user_id')->index('member');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('poll_logs');
    }
};
