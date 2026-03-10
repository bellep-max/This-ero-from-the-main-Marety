<?php

use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('price', 10)->nullable()->default(0.00);
            $table->string('description');
            $table->foreignIdFor(Group::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('active')->default(0);
            $table->boolean('trial')->default(0);
            $table->smallInteger('trial_period')->nullable();
            $table->enum('trial_period_format', ['D', 'W', 'M', 'Y'])->nullable();
            $table->smallInteger('plan_period')->default(0);
            $table->enum('plan_period_format', ['D', 'W', 'M', 'Y']);
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
