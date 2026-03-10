<?php

use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_plans')) {
            Schema::create('musicengine_plans', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['site', 'user'])->default('site');
            $table->string('paypal_id')->unique();
            $table->foreignIdFor(Group::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->float('price')->unsigned();
            $table->unsignedTinyInteger('percentage')->nullable();
            $table->enum('interval', ['day', 'week', 'month', 'year'])->default('month');
            $table->enum('status', ['created', 'inactive', 'active'])->default('active');
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_plans');
    }
};
