<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Video serisi mi (Bölüm 8 /video + §14.3) yoksa yalnız TV programı mı.
            $table->boolean('is_series')->default(false)->after('is_live');
            $table->index('is_series');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['is_series']);
            $table->dropColumn('is_series');
        });
    }
};