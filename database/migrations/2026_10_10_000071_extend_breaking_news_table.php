<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breaking_news', function (Blueprint $table) {
            $table->string('short_code')->nullable()->unique()->after('title');
            $table->date('date_group')->nullable()->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('breaking_news', function (Blueprint $table) {
            $table->dropColumn(['short_code', 'date_group']);
        });
    }
};