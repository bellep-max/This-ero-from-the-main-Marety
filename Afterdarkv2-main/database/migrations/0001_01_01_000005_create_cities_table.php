<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_cities')) {
            Schema::create('musicengine_cities', function (Blueprint $table) {
                $table->id();
                $table->char('name', 35);
                $table->foreignIdFor(Country::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->char('district', 20)->nullable();
                $table->boolean('fixed')->default(0)->index();
                $table->boolean('is_visible')->default(1)->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_cities');
    }
};
