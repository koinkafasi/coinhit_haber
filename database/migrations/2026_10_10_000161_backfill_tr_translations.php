<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mevcut TR içeriğini kaybetmeden çeviri tablolarına taşır (master spec §18.8).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('news')->orderBy('id')->select(['id', 'title', 'slug', 'spot', 'body'])
            ->chunk(200, function ($rows) use ($now) {
                $insert = [];
                foreach ($rows as $row) {
                    $insert[] = [
                        'news_id' => $row->id,
                        'locale' => 'tr',
                        'title' => $row->title,
                        'slug' => $row->slug,
                        'summary' => $row->spot,
                        'body' => $row->body,
                        'meta_title' => $row->title,
                        'meta_description' => mb_substr(strip_tags((string) $row->spot), 0, 255),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($insert !== []) {
                    DB::table('news_translations')->insertOrIgnore($insert);
                }
            });

        DB::table('categories')->orderBy('id')->select(['id', 'name', 'slug'])
            ->chunk(200, function ($rows) use ($now) {
                $insert = [];
                foreach ($rows as $row) {
                    $insert[] = [
                        'category_id' => $row->id,
                        'locale' => 'tr',
                        'name' => $row->name,
                        'slug' => $row->slug,
                        'description' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($insert !== []) {
                    DB::table('category_translations')->insertOrIgnore($insert);
                }
            });

        DB::table('authors')->orderBy('id')->select(['id', 'name', 'slug', 'title', 'bio'])
            ->chunk(200, function ($rows) use ($now) {
                $insert = [];
                foreach ($rows as $row) {
                    $insert[] = [
                        'author_id' => $row->id,
                        'locale' => 'tr',
                        'name' => $row->name,
                        'bio' => $row->bio,
                        'title' => $row->title,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($insert !== []) {
                    DB::table('author_translations')->insertOrIgnore($insert);
                }
            });

        DB::table('breaking_news')->orderBy('id')->select(['id', 'title'])
            ->chunk(200, function ($rows) use ($now) {
                $insert = [];
                foreach ($rows as $row) {
                    $insert[] = [
                        'breaking_news_id' => $row->id,
                        'locale' => 'tr',
                        'text' => $row->title,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($insert !== []) {
                    DB::table('breaking_news_translations')->insertOrIgnore($insert);
                }
            });

        DB::table('site_settings')->orderBy('id')->select(['key', 'value'])
            ->chunk(200, function ($rows) use ($now) {
                $insert = [];
                foreach ($rows as $row) {
                    $insert[] = [
                        'key' => $row->key,
                        'locale' => 'tr',
                        'value' => $row->value,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($insert !== []) {
                    DB::table('site_settings_translations')->insertOrIgnore($insert);
                }
            });
    }

    public function down(): void
    {
        DB::table('news_translations')->where('locale', 'tr')->delete();
        DB::table('category_translations')->where('locale', 'tr')->delete();
        DB::table('author_translations')->where('locale', 'tr')->delete();
        DB::table('breaking_news_translations')->where('locale', 'tr')->delete();
        DB::table('site_settings_translations')->where('locale', 'tr')->delete();
    }
};