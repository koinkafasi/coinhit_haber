<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipo_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('ticker')->nullable();
            $table->string('request_start_date')->nullable();
            $table->string('request_end_date')->nullable();
            $table->decimal('lot_value', 20, 2)->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('status')->default('planned');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipo_calendars');
    }
};