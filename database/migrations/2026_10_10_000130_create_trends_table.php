<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trends', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('news_trend', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trend_id')->constrained('trends')->cascadeOnDelete();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['trend_id', 'news_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_trend');
        Schema::dropIfExists('trends');
    }
};