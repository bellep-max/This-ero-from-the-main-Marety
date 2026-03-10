<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('collections')) {
            Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('collectionable_id');
            $table->string('collectionable_type', 50);
            $table->timestamps();
            $table->unique(['user_id', 'collectionable_id', 'collectionable_type']);
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
