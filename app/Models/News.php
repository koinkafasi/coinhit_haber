<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\Locale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory, HasTranslations;

    protected $with = ['translations'];

    protected $fillable = [
        'news_id',
        'category_id',
        'author_id',
        'title',
        'slug',
        'spot',
        'body',
        'inline_images',
        'embeds',
        'image',
        'image_og',
        'source',
        'tags',
        'keywords',
        'status',
        'is_breaking',
        'is_featured',
        'views',
        'comment_count',
        'word_count',
        'has_video',
        'video_url',
        'editor_id',
        'published_at',
        'modified_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'keywords' => 'array',
        'inline_images' => 'array',
        'embeds' => 'array',
        'status' => 'string',
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'has_video' => 'boolean',
        'views' => 'integer',
        'comment_count' => 'integer',
        'word_count' => 'integer',
        'published_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function trends(): BelongsToMany
    {
        return $this->belongsToMany(Trend::class, 'news_trend')->withTimestamps();
    }

    /**
     * @return array<string, string>
     */
    public function translatable(): array
    {
        return [
            'title' => 'title',
            'slug' => 'slug',
            'spot' => 'summary',
            'body' => 'body',
        ];
    }

    public function translationModel(): string
    {
        return NewsTranslation::class;
    }

    public function translationForeignKey(): string
    {
        return 'news_id';
    }

    public function getMetaTitleAttribute(): string
    {
        return (string) ($this->translationValue('meta_title') ?? $this->title);
    }

    public function getMetaDescriptionAttribute(): string
    {
        return (string) ($this->translationValue('meta_description') ?? strip_tags((string) $this->spot));
    }

    public function getUrlAttribute(): string
    {
        return Locale::url('/'.$this->slug.'-'.$this->news_id);
    }

    /**
     * Kısa link kodu (spec §9 — `bb.ht/{kod}`); numeric id taban-36 karşılığı.
     */
    public function getShortlinkAttribute(): string
    {
        return 'bb.ht/'.strtolower(base_convert((string) $this->news_id, 10, 36));
    }

    /**
     * Kısa linkin tam adresi.
     */
    public function getShortlinkUrlAttribute(): string
    {
        return 'https://'.$this->shortlink;
    }

    /**
     * Spot'u madde işaretli satırlara ayırır (`ul > li > h2.description`, spec §9).
     *
     * @return array<int, string>
     */
    public function spotItems(): array
    {
        $items = [];

        foreach (preg_split('/\r?\n/', (string) $this->spot) as $line) {
            $line = trim(preg_replace('/^[-•*]\s*/u', '', $line) ?? $line);

            if ($line !== '') {
                $items[] = $line;
            }
        }

        return $items;
    }

    /**
     * Bir paragraf metnini güvenli HTML'e çevirir; gövde anahtar kelimelerini
     * `/haberleri/{etiket}` bağlantılarına dönüştürür (spec §9).
     */
    public function linkedParagraph(string $text): string
    {
        $safe = e($text);
        $keywords = array_values(array_unique(array_filter(
            array_map('strval', array_merge((array) $this->keywords, (array) $this->tags))
        )));

        if (empty($keywords)) {
            return $safe;
        }

        usort($keywords, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);

            if (mb_strlen($keyword) < 3) {
                continue;
            }

            $anchor = '<a href="'.Locale::url('/haberleri/'.Str::slug($keyword)).'">$1</a>';
            $result = preg_replace('/('.preg_quote(e($keyword), '/').')/iu', $anchor, $safe, 1, $count);

            if ($count > 0) {
                $safe = (string) $result;
            }
        }

        return $safe;
    }

    /**
     * Sonsuz kaydırma `data-it` yükü (spec §9, §15.15).
     *
     * @return array<string, string>
     */
    public function infiniteData(int $index = 1): array
    {
        $nextId = (int) $this->news_id - 1;

        return [
            'index' => (string) $index,
            'id' => (string) $this->news_id,
            'next' => $nextId > 0 ? Locale::url('/infinite/news/'.$nextId) : '',
            'title' => (string) $this->title,
            'slug' => (string) $this->slug,
            'url' => $this->url,
        ];
    }

    /**
     * Gövde metninin uzunluğu (analitik `textLength`, spec §11.4).
     */
    public function textLength(): int
    {
        return mb_strlen(strip_tags((string) $this->body));
    }

    /**
     * Gövde sözcük sayısı (analitik `wordCount`); kayıtlı değer yoksa hesaplar.
     */
    public function wordCount(): int
    {
        if ($this->word_count > 0) {
            return (int) $this->word_count;
        }

        return str_word_count(strip_tags((string) $this->body), 0, 'çğıöşüÇĞİÖŞÜ');
    }

    /**
     * Gövdeyi paragraf ve iç görsel/embed bloklarına ayırır (spec §9).
     *
     * @return array<int, array{type: string, html?: string, src?: string, alt?: string, data_src?: string}>
     */
    public function contentBlocks(): array
    {
        $blocks = [];

        foreach (preg_split('/\n\s*\n/', (string) $this->body) as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $blocks[] = ['type' => 'paragraph', 'html' => $paragraph];
        }

        foreach ((array) $this->inline_images as $image) {
            if (! is_array($image) || empty($image['src'])) {
                continue;
            }

            $blocks[] = [
                'type' => 'image',
                'src' => (string) $image['src'],
                'alt' => (string) ($image['alt'] ?? ''),
                'class' => (string) ($image['class'] ?? ''),
            ];
        }

        foreach ((array) $this->embeds as $embed) {
            if (! is_array($embed) || empty($embed['src'])) {
                continue;
            }

            $blocks[] = [
                'type' => 'flourish',
                'data_src' => (string) $embed['src'],
                'title' => (string) ($embed['title'] ?? ''),
            ];
        }

        return $blocks;
    }

    /**
     * Kategori etiketinden başlık sonekini üretir (spec §11.1).
     */
    public function titleSuffix(): string
    {
        $name = (string) ($this->category?->name ?? '');

        if ($name === '') {
            return 'haberleri';
        }

        if (Str::contains(Str::lower($name), ['son dakika', 'gündem'])) {
            return 'Son dakika ekonomi haberleri';
        }

        return $name.' haberleri';
    }
}
