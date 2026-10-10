<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->json('inline_images')->nullable()->after('body');
            $table->json('embeds')->nullable()->after('inline_images');
            $table->json('keywords')->nullable()->after('tags');
            $table->unsignedInteger('comment_count')->default(0)->after('views');
            $table->unsignedInteger('word_count')->default(0)->after('comment_count');
            $table->boolean('has_video')->default(false)->after('word_count');
            $table->string('video_url')->nullable()->after('has_video');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn([
                'inline_images',
                'embeds',
                'keywords',
                'comment_count',
                'word_count',
                'has_video',
                'video_url',
            ]);
        });
    }
};