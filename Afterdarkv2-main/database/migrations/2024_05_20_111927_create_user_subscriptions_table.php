<?php

use App\Models\MEPlan;
use App\Models\MESubscription;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('musicengine_user_subscriptions')) {
            Schema::create('musicengine_user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MEPlan::class, 'plan_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'subscribed_user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(MESubscription::class, 'subscription_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->decimal('amount')->unsigned()->nullable();
            $table->string('currency', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('musicengine_user_subscriptions');
    }
};
