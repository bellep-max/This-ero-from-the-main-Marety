<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('artists')) {
            Schema::create('artists', function (Blueprint $table) {
                $table->id();
                $table->string('mood', 50)->nullable()->index();
                $table->text('bio')->nullable();
                $table->string('name');
                $table->string('location')->nullable();
                $table->string('website')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->smallInteger('loves')->default(0);
                $table->boolean('allow_comments')->default(1);
                $table->smallInteger('comment_count')->nullable()->default(0);
                $table->boolean('is_visible')->default(1)->index();
                $table->boolean('verified')->default(0)->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
