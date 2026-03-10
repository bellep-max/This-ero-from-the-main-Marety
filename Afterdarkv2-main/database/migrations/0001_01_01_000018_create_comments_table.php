<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->mediumInteger('reply_count')->default(0);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('commentable_id')->nullable()->index();
            $table->string('commentable_type', 50)->nullable()->index();
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->text('content')->nullable()->collation('utf8mb4_bin');
            } else {
                $table->text('content')->nullable();
            }
            $table->tinyInteger('edited')->default(0);
            $table->string('ip', 46)->nullable();
            $table->mediumInteger('reaction_count')->default(0);
            $table->boolean('approved')->default(1)->index();
            $table->timestamps();
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
