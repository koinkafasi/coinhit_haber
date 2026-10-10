<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('podcast_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained('podcasts')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('audio_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['podcast_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcast_episodes');
    }
};