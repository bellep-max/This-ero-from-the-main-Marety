<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('type', ['percentage', 'fixed'])->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->integer('use_count')->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('minimum_spend')->nullable();
            $table->integer('maximum_spend')->nullable();
            $table->string('access')->nullable();
            $table->boolean('approved')->default(1);
            $table->timestamps();
            $table->timestamp('expired_at')->nullable();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
