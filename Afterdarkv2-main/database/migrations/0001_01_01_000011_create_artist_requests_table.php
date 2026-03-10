<?php

use App\Models\Artist;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('artist_requests')) {
            Schema::create('artist_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Artist::class)->nullable()->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('artist_name');
            $table->string('phone', 50)->nullable();
            $table->string('ext', 10)->nullable();
            $table->string('affiliation')->nullable();
            $table->text('message')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->boolean('approved')->default(0)->index();
            $table->timestamps();
        });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('artist_requests');
    }
};
