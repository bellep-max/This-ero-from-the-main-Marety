<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('post_tags')) {
            Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Post::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('tag')->default('')->index();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('post_tags');
    }
};
