<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('slides')) {
            Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('priority')->nullable()->default(0)->index();
            $table->integer('object_id');
            $table->string('object_type', 50)->default('');
            $table->string('title')->nullable();
            $table->string('title_link')->nullable();
            $table->text('description')->nullable();
            $table->boolean('allow_home')->default(0)->index();
            $table->boolean('allow_discover')->default(0)->index();
            $table->boolean('allow_radio')->default(0);
            $table->boolean('allow_community')->default(0)->index();
            $table->boolean('allow_podcasts')->default(0)->index();
            $table->boolean('allow_trending')->default(0)->index();
            $table->string('mood', 50)->nullable()->index();
            $table->string('radio', 50)->nullable()->index('radio');
            $table->string('podcast', 50)->nullable()->index('podcast');
            $table->boolean('is_visible')->default(1)->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
