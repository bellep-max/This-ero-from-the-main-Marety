<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('oauth_socialite')) {
            Schema::create('oauth_socialite', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('provider_id', 50)->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_email')->nullable();
            $table->string('provider_artwork');
            $table->string('service', 50)->nullable();
            $table->boolean('autopost')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at');
            $table->unique(['user_id', 'provider_id', 'service'], 'UNIQUE_SERVICE');
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('oauth_socialite');
    }
};
