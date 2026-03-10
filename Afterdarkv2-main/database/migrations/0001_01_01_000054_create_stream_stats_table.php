<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stream_stats')) {
            Schema::create('stream_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('streamable_id');
            $table->string('streamable_type', 50);
            $table->decimal('revenue', 10, 6)->default(0.000000);
            $table->string('ip', 46)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'streamable_type'], 'USER_STREAM_TYPE');
            $table->unique(['streamable_id', 'streamable_type', 'ip'], 'UNIQUE_STREAM');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('stream_stats');
    }
};
