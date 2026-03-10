<?php

use App\Models\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_countries')) {
            Schema::create('musicengine_countries', function (Blueprint $table) {
                $table->id();
                $table->char('code', 3)->default('')->index();
                $table->char('name', 52)->default('');
                $table->string('continent', 50)->nullable()->index('continent');
                $table->foreignIdFor(Region::class)->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->char('local_name', 45)->default('');
                $table->char('government_form', 45)->default('')->index('government_form');
                $table->char('code2', 2)->default('');
                $table->boolean('fixed')->default(0)->index();
                $table->boolean('is_visible')->default(1)->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_countries');
    }
};
