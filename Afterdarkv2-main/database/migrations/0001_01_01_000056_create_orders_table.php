<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('orderable_id');
            $table->string('orderable_type', 50)->nullable();
            $table->string('payment', 20)->nullable();
            $table->decimal('amount', 10)->unsigned()->nullable();
            $table->decimal('commission', 10)->default(0.00);
            $table->string('currency', 50)->index('currency');
            $table->boolean('payment_status')->default(0)->index('payment_status');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
