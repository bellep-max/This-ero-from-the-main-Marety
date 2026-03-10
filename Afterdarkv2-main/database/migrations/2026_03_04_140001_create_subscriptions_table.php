<?php

///////////////////////////////////////////////////////////////////////
//
// Migration autoloading for lucasdotvin/Soulbscription is disabled!
//
// This file replaces a stock migration and should be manually upgraded.
///////////////////////////////////////////////////////////////////////

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscr_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Subscriptions\Plan::class);

            if (config('soulbscription.models.subscriber.uses_uuid')) {
                $table->uuidMorphs('subscriber');
            } else {
                $table->numericMorphs('subscriber');
            }

            $table->boolean('was_switched')->default(false);
            $table->date('started_at');
            $table->timestamps();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('grace_days_ended_at')->nullable();
            $table->timestamp('suppressed_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscr_subscriptions');
    }
};
