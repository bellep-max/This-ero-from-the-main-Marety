<?php

use App\Models\MEPlan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_subscriptions')) {
            Schema::create('musicengine_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(MEPlan::class, 'plan_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('subscription_id')->nullable();
            $table->string('status');
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->double('amount')->unsigned()->default(0.00);
            $table->string('currency', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_subscriptions');
    }
};
