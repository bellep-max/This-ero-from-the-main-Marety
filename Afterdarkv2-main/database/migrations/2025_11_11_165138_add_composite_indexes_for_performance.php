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
        $connection = Schema::getConnection();

        Schema::table('songs', function (Blueprint $table) use ($connection) {
            $indexName = 'songs_user_id_is_visible_approved_index';
            if (!$this->indexExists($connection, 'songs', $indexName)) {
                $table->index(['user_id', 'is_visible', 'approved'], $indexName);
            }

            $indexName = 'songs_is_visible_approved_created_at_index';
            if (!$this->indexExists($connection, 'songs', $indexName)) {
                $table->index(['is_visible', 'approved', 'created_at'], $indexName);
            }
        });

        Schema::table('orders', function (Blueprint $table) use ($connection) {
            $indexName = 'orders_user_id_payment_status_created_at_index';
            if (!$this->indexExists($connection, 'orders', $indexName)) {
                $table->index(['user_id', 'payment_status', 'created_at'], $indexName);
            }

            $indexName = 'orders_orderable_type_orderable_id_payment_status_index';
            if (!$this->indexExists($connection, 'orders', $indexName)) {
                $table->index(['orderable_type', 'orderable_id', 'payment_status'], $indexName);
            }
        });

        Schema::table('musicengine_subscriptions', function (Blueprint $table) use ($connection) {
            // Check if 'status' column exists, if not use 'payment_status'
            $hasStatus = $this->columnExists($connection, 'musicengine_subscriptions', 'status');
            $statusColumn = $hasStatus ? 'status' : 'payment_status';
            $indexName = 'subscriptions_user_id_' . $statusColumn . '_next_billing_date_index';
            if (!$this->indexExists($connection, 'musicengine_subscriptions', $indexName)) {
                $table->index(['user_id', $statusColumn, 'next_billing_date'], $indexName);
            }
        });

        Schema::table('activities', function (Blueprint $table) use ($connection) {
            $indexName = 'activities_user_id_created_at_index';
            if (!$this->indexExists($connection, 'activities', $indexName)) {
                $table->index(['user_id', 'created_at'], $indexName);
            }
        });

        Schema::table('comments', function (Blueprint $table) use ($connection) {
            $indexName = 'comments_commentable_type_commentable_id_created_at_index';
            if (!$this->indexExists($connection, 'comments', $indexName)) {
                $table->index(['commentable_type', 'commentable_id', 'created_at'], $indexName);
            }
        });
    }

    private function indexExists($connection, $table, $indexName): bool
    {
        $database = $connection->getDatabaseName();
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $indexName]
        );
        return $result[0]->count > 0;
    }

    private function columnExists($connection, $table, $column): bool
    {
        $database = $connection->getDatabaseName();
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.columns
             WHERE table_schema = ? AND table_name = ? AND column_name = ?",
            [$database, $table, $column]
        );
        return $result[0]->count > 0;
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_visible', 'approved']);
            $table->dropIndex(['is_visible', 'approved', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'payment_status', 'created_at']);
            $table->dropIndex(['orderable_type', 'orderable_id', 'payment_status']);
        });

        Schema::table('musicengine_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status', 'next_billing_date']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['commentable_type', 'commentable_id', 'created_at']);
        });
    }
};
