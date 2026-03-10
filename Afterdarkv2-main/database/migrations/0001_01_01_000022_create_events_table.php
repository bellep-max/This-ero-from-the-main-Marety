<?php

use App\Models\Artist;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('link')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->boolean('is_visible')->default(1)->index();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
