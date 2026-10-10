<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Yatırım fonları kataloğu (spec §5 fon verisi, §8 /yatirim-fonlari).
     *
     * Fon fiyatı, günlük TL/USD değişimleri, dönemsel getiri matrisi
     * (günlük→5 yıllık), risk değeri ve portföy dağılımı burada tutulur.
     */
    public function up(): void
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('fund_class');
            $table->string('type')->nullable();
            $table->decimal('price', 18, 6)->default(0);
            $table->decimal('change_tl_percent', 8, 2)->default(0);
            $table->decimal('change_usd_percent', 8, 2)->default(0);
            $table->decimal('return_daily', 8, 2)->default(0);
            $table->decimal('return_weekly', 8, 2)->default(0);
            $table->decimal('return_monthly', 8, 2)->default(0);
            $table->decimal('return_quarterly', 8, 2)->default(0);
            $table->decimal('return_semiannual', 8, 2)->default(0);
            $table->decimal('return_yearly', 8, 2)->default(0);
            $table->decimal('return_3y', 8, 2)->default(0);
            $table->decimal('return_5y', 8, 2)->default(0);
            $table->unsignedTinyInteger('risk_value')->default(3);
            $table->unsignedBigInteger('investor_count')->default(0);
            $table->decimal('portfolio_value', 22, 2)->default(0);
            $table->json('portfolio')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['fund_class', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funds');
    }
};