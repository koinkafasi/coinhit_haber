<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kap_disclosures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kap_id')->unique();
            $table->string('company_code');
            $table->string('company_name');
            $table->string('company_slug');
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['company_slug']);
            $table->index(['published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kap_disclosures');
    }
};