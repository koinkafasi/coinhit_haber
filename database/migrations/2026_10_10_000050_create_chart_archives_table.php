<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chart_id')->unique();
            $table->string('category')->default('Grafik');
            $table->string('title');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->text('summary')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_archives');
    }
};
