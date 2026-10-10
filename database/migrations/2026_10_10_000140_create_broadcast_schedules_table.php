<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->string('air_time', 5);
            $table->string('program_name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['day_of_week', 'air_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_schedules');
    }
};