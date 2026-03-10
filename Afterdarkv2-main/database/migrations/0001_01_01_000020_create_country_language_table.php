<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_country_languages')) {
            Schema::create('musicengine_country_languages', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Country::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->char('name', 30)->default('')->index();
                $table->enum('is_official', ['T', 'F'])->default('F');
                $table->boolean('fixed')->default(0)->index();
                $table->boolean('is_visible')->default(1)->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_country_languages');
    }
};
