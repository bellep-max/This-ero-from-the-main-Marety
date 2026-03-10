<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bans')) {
            Schema::create('bans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->string('ip', 46)->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('bans');
    }
};
