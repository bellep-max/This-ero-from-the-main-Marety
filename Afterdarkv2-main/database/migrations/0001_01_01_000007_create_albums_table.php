<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('albums')) {
            Schema::create('albums', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->decimal('price', 10)->nullable()->default(0.00);
                $table->boolean('selling')->default(0)->index();
                $table->string('genre', 50)->nullable()->index('genre');
                $table->string('mood', 50)->nullable()->index();
                $table->tinyInteger('type')->nullable()->index();
                $table->string('artistIds', 50)->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->string('copyright', 50)->default('');
                $table->boolean('allow_comments')->default(1);
                $table->mediumInteger('comment_count')->default(0);
                $table->boolean('is_visible')->default(1)->index();
                $table->boolean('approved')->default(0)->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
