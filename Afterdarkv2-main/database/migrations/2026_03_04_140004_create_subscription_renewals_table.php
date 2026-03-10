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

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscr_subscription_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Subscriptions\Subscription::class);
            $table->boolean('overdue');
            $table->boolean('renewal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscr_subscription_renewals');
    }
};
