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
        Schema::create('subscr_feature_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Subscriptions\Feature::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Subscriptions\Plan::class)->constrained()->cascadeOnDelete();
            $table->decimal('charges', 14, 6)->nullable();
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
        Schema::dropIfExists('subscr_feature_plan');
    }
};
