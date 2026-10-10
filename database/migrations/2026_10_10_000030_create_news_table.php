<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('news_id')->unique();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('spot')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_og')->nullable();
            $table->string('source')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('published');
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('editor_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('modified_at')->nullable();
            $table->timestamps();

            $table->index(['slug']);
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
