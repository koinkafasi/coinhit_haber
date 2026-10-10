<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Çok dilli dinamik içerik tabloları (master spec §18.5).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['news_id', 'locale']);
            $table->index('locale');
            $table->index('slug');
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'locale']);
            $table->index('locale');
            $table->index('slug');
        });

        Schema::create('author_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(['author_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('breaking_news_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breaking_news_id')->constrained('breaking_news')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('text');
            $table->timestamps();

            $table->unique(['breaking_news_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('site_settings_translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('locale', 5);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['key', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings_translations');
        Schema::dropIfExists('breaking_news_translations');
        Schema::dropIfExists('author_translations');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('news_translations');
    }
};