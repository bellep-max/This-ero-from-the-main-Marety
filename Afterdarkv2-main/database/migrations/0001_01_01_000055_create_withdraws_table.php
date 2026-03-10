<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('withdraws')) {
            Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('amount', 10);
            $table->boolean('paid')->default(0)->index('paid');
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
