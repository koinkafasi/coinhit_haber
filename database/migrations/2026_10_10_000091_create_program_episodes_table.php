<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('guest')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamp('air_at')->nullable();
            $table->timestamps();

            $table->index(['program_id', 'air_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_episodes');
    }
};