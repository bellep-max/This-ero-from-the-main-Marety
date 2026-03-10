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

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscr_features', function (Blueprint $table) {
            $table->id();
            $table->integer('periodicity')->unsigned()->nullable();
            $table->boolean('consumable');
            $table->boolean('quota')->default(false);
            $table->boolean('postpaid')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->string('periodicity_type')->nullable();
            $table->string('name');
            $table->string('label');
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
        Schema::dropIfExists('subscr_features');
    }
};
