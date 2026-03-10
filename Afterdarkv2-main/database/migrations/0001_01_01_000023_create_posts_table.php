<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('alt_name')->default('')->index();
            $table->mediumText('short_content')->nullable();
            $table->mediumText('full_content')->nullable();
            $table->foreignIdFor(Category::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('view_count')->nullable()->default(0);
            $table->boolean('allow_comments')->default(1);
            $table->unsignedMediumInteger('comment_count')->default(0)->index();
            $table->boolean('allow_main')->unsigned()->default(1)->index();
            $table->boolean('disable_index')->nullable()->default(0);
            $table->boolean('fixed')->default(0)->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('access')->nullable();
            $table->boolean('is_visible')->default(1)->index();
            $table->boolean('approved')->default(0)->index();
            $table->timestamps();

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['short_content', 'full_content', 'title']);
            }
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
