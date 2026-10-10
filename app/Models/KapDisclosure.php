<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KapDisclosure extends Model
{
    /** @use HasFactory<KapDisclosureFactory> */
    use HasFactory;

    /**
     * KAP TR/EN ikili alan sözlüğü (spec §4).
     */
    public const FIELD_DICTIONARY = [
        'CONSOLIDATION_METHOD' => ['Konsolidasyon Yöntemi', 'Consolidation Method'],
        'MaterialEventDisclosureGeneralAbstract' => ['Özel Durum Açıklaması (Genel) Özeti', 'Material Event Disclosure (General) Abstract'],
        'UpdateNotificationFlag' => ['Güncelleme Bildirimi', 'Update Notification Flag'],
        'CorrectionNotificationFlag' => ['Düzeltme Bildirimi', 'Correction Notification Flag'],
        'DateOfThePreviousNotification' => ['Önceki Bildirimin Tarihi', 'Date Of The Previous Notification'],
        'DelayedAnnouncementFlag' => ['Ertelenmiş Açıklama', 'Delayed Announcement Flag'],
        'ExplanationTextBlock' => ['Açıklama Metni', 'Explanation Text Block'],
    ];

    protected $fillable = [
        'kap_id',
        'company_code',
        'company_name',
        'company_slug',
        'title',
        'category',
        'summary',
        'body',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(KapField::class)->orderBy('sort_order');
    }

    /**
     * KAP detay rotası (spec §8 / §14.5): `/borsa/hisse/{firma}/kap-haberi/{kapId}`.
     */
    public function getUrlAttribute(): string
    {
        return '/borsa/hisse/'.$this->company_slug.'/kap-haberi/'.$this->kap_id;
    }
}