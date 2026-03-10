<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('musicengine_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('musicengine_subscriptions', 'status')) {
                $table->string('status', 50)->nullable()->after('payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musicengine_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('musicengine_subscriptions', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
