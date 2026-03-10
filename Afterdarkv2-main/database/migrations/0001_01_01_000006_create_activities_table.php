<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->integer('activityable_id')->nullable()->index('activityable_id');
                $table->string('activityable_type', 50)->nullable();
                $table->string('action', 20)->index();
                $table->boolean('allow_comments')->default(1);
                $table->mediumInteger('comment_count')->default(0);
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->index(['user_id', 'activityable_type', 'action'], 'COLLAPSE_QUERY_INDEX');
                $table->index(['activityable_id', 'activityable_type']);

            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
