<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_instrument_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 24, 8);
            $table->decimal('market_cap', 28, 2)->nullable();
            $table->decimal('volume_24h', 28, 2)->nullable();
            $table->decimal('change_24h', 12, 4)->nullable();
            $table->decimal('change_7d', 12, 4)->nullable();
            $table->decimal('change_30d', 12, 4)->nullable();
            $table->decimal('high_24h', 24, 8)->nullable();
            $table->decimal('low_24h', 24, 8)->nullable();
            $table->json('sparkline_7d')->nullable();
            $table->string('source', 32)->default('coingecko');
            $table->timestamp('captured_at')->index();
            $table->timestamps();

            $table->index(['market_instrument_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_snapshots');
    }
};
