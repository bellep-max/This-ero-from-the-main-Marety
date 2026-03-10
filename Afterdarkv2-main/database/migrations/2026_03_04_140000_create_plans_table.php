<?php

///////////////////////////////////////////////////////////////////////
//
// Migration autoloading for lucasdotvin/Soulbscription is disabled!
//
// This file replaces a stock migration and should be manually upgraded.
///////////////////////////////////////////////////////////////////////

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\SubscriptionPlanConstants;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscr_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('grace_days')->default(0);
            $table->integer('periodicity')->unsigned()->nullable();
            $table->decimal('price', 14, 6)->default(0);
            $table->boolean('is_subscribable')->default(0); // choosable for paid accounts
            $table->boolean('is_cancelable')->default(0); // able to cancel
            $table->boolean('is_visible')->default(0); // visible to users
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('currency')->default(SubscriptionPlanConstants::PRICING_CURRENCY);
            $table->string('periodicity_type')->nullable();
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscr_plans');
    }
};
