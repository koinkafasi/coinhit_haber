<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_instruments', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('code');
            $table->string('name');
            $table->string('slug');
            $table->string('currency', 8)->default('TRY');
            $table->unsignedTinyInteger('decimals')->default(2);
            $table->string('unit')->nullable();
            $table->decimal('base_price', 20, 6)->default(0);
            $table->decimal('previous_close', 20, 6)->default(0);
            $table->json('meta')->nullable();
            $table->string('source')->default('foreks');
            $table->unsignedInteger('delay_minutes')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['group', 'code']);
            $table->index(['group', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_instruments');
    }
};