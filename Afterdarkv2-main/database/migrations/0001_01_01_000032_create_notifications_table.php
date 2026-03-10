<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('object_id')->nullable()->index();
            $table->integer('notificationable_id');
            $table->string('notificationable_type', 50)->nullable();
            $table->integer('hostable_id');
            $table->string('hostable_type', 50)->nullable();
            $table->string('action', 30)->index();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->index(['notificationable_id', 'notificationable_type']);
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
