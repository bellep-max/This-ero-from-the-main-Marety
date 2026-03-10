<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('channels')) {
            Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('priority')->default(0)->index();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('alt_name')->index();
            $table->string('object_ids')->nullable();
            $table->string('type', 20);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('allow_home')->default(0)->index();
            $table->boolean('allow_discover')->default(0)->index();
            $table->boolean('allow_radio')->default(0)->index('allow_radio');
            $table->boolean('allow_community')->default(0)->index();
            $table->boolean('allow_trending')->default(0)->index();
            $table->boolean('allow_podcasts')->default(0)->index();
            $table->string('mood', 50)->nullable()->index();
            $table->boolean('is_visible')->default(1)->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
