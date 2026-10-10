<?php

namespace App\Services\Analytics;

use App\Models\News;

/**
 * `window.customdimension` analitik yükünü üretir (Bölüm 11.4).
 *
 * Ogezio / Gemius / GTM ölçümlerinde kullanılan alanlar sayfa tipine göre
 * hesaplanır; makale sayfalarında haber meta verisinden türetilir.
 */
class CustomDimensionBuilder
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function build(?News $news = null, string $pageType = 'home', array $context = []): array
    {
        $dimension = array_merge($this->defaults($pageType), $context);

        if ($news !== null) {
            $dimension = array_merge($dimension, $this->fromNews($news));
        }

        return $dimension;
    }

    /**
     * @return array<string, mixed>
     */
    private function defaults(string $pageType): array
    {
        return [
            'newsID' => '',
            'newsTitle' => '',
            'editorID' => '',
            'detailType' => $pageType,
            'pageType' => $pageType,
            'textLength' => 0,
            'wordCount' => 0,
            'commentCount' => 0,
            'videoIncluded' => false,
            'datePublished' => '',
            'dateModified' => '',
            'columnistName' => '',
            'hierarchy' => '',
            'mainCategory' => '',
            'mainCategory2' => '',
            'category1' => '',
            'category2' => '',
            'category3' => '',
            'category4' => '',
            'layoutType' => 'default',
            'photoTotalCount' => 0,
            'adBlockUser' => false,
            'loginStatus' => 'guest',
            'memberId' => '',
            'gender' => '',
            'platform' => 'web',
            'cinerSource' => 'bloomberght',
            'nativeAd' => '',
            'webVersion' => config('site.web_version', '127'),
            'ageGroup' => '',
            'disYazarlar' => '',
            'isSpecialNews' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fromNews(News $news): array
    {
        $hierarchy = $this->hierarchy($news);

        return [
            'newsID' => (string) $news->news_id,
            'newsTitle' => (string) $news->title,
            'editorID' => (string) ($news->editor_id ?? ''),
            'detailType' => 'news_detail',
            'pageType' => 'article',
            'textLength' => $news->textLength(),
            'wordCount' => $news->wordCount(),
            'commentCount' => (int) $news->comment_count,
            'videoIncluded' => (bool) $news->has_video,
            'datePublished' => $news->published_at?->format('Y-m-d\TH:i:sP') ?? '',
            'dateModified' => $news->modified_at?->format('Y-m-d\TH:i:sP') ?? '',
            'columnistName' => (string) ($news->author?->name ?? ''),
            'hierarchy' => implode(' > ', $hierarchy),
            'mainCategory' => (string) ($news->category?->name ?? ''),
            'mainCategory2' => (string) ($news->category?->parent?->name ?? ''),
            'category1' => $hierarchy[0] ?? '',
            'category2' => $hierarchy[1] ?? '',
            'category3' => $hierarchy[2] ?? '',
            'category4' => $hierarchy[3] ?? '',
            'disYazarlar' => (string) ($news->author?->name ?? ''),
            'isSpecialNews' => (bool) $news->is_featured,
        ];
    }

    /**
     * Kök → yaprak kategori zinciri.
     *
     * @return array<int, string>
     */
    private function hierarchy(News $news): array
    {
        $chain = [];
        $category = $news->category;

        while ($category !== null) {
            array_unshift($chain, (string) $category->name);
            $category = $category->parent;
        }

        return $chain;
    }
}