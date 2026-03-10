<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //        if (Schema::hasColumn('user_subscriptions', 'plan_id')) {
        //            Schema::table('user_subscriptions', function (Blueprint $table) {
        //                $table->dropConstrainedForeignId('plan_id');
        //            });
        //        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('musicengine_user_subscriptions', 'plan_id')) {
            Schema::table('musicengine_user_subscriptions', function (Blueprint $table) {
                $table->foreignId('plan_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            });
        }
    }
};
