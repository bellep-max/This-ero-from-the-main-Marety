<?php

use App\Models\City;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stations')) {
            Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignIdFor(Country::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Language::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('stream_url');
            $table->boolean('allow_comments')->default(1)->index();
            $table->mediumInteger('comment_count')->default(0);
            $table->integer('play_count')->default(0)->index();
            $table->integer('failed_count')->default(0)->index();
            $table->boolean('is_visible')->default(1);
            $table->timestamps();

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'description']);
            }
        });    
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
