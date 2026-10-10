<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public const SETTINGS = [
        ['site_name', 'Bloomberg HT', 'brand'],
        ['site_tagline', "Türkiye'nin ekonomi platformu", 'brand'],
        ['logo_path', '/brand/logo.svg', 'brand'],
        ['logo_alt', 'Bloomberg HT', 'brand'],
        ['favicon_path', '/favicon.ico', 'brand'],
        ['apple_touch_icon', '/apple-touch-icon.png', 'brand'],
        ['default_og_image', '/img/og-default.png', 'brand'],
        ['og_locale', 'tr_TR', 'brand'],
        ['language', 'tr', 'brand'],

        ['company_name', 'C Görsel Yayınlar A.Ş.', 'contact'],
        ['legal_representative', 'Ergün Örentel', 'contact'],
        ['address', 'Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul', 'contact'],
        ['phone', '+90 212 313 6000', 'contact'],
        ['editor_email', 'editor@bloomberght.com', 'contact'],
        ['ad_email', 'dijitalreklam@cyh.com.tr', 'contact'],
        ['kep_address', 'cgorsel@hs03.kep.tr', 'contact'],

        ['copyright', 'YASAL UYARI © 2026', 'legal'],
        ['legal_notice', 'Sitemizdeki veriler Foreks tarafından sağlanmaktadır. NASDAQ, Dow Jones Industrial Average, SHCOM, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VİOP, Tahvil-bono verileri 15 dk; CME, NYMEX ve S&P verileri 10 dk gecikmelidir.', 'legal'],
        ['data_license', 'CME verilerinin izinsiz kullanımı yasaktır.', 'legal'],
        ['no_ai_training', 'Bu sitedeki içerik, hiçbir yapay zeka modelinin eğitiminde kullanılamaz.', 'legal'],

        ['social_twitter', 'https://x.com/bloomberght', 'social'],
        ['social_facebook', 'https://www.facebook.com/bloomberght', 'social'],
        ['social_instagram', 'https://www.instagram.com/bloomberght', 'social'],
        ['social_youtube', 'https://www.youtube.com/user/bloomberghttv', 'social'],

        ['gemius_id', 'demo', 'analytics'],
        ['gtm_id', 'GTM-5XBP88', 'analytics'],
        ['assets_version', '127', 'analytics'],

        ['brand_list', 'Habertürk, Habertürk TV, HT Spor, Bloomberg HT, Businessweek, Show TV, Show Türk, Show Max, Can Medya Grubu', 'footer'],
    ];

    public function run(): void
    {
        foreach (self::SETTINGS as [$key, $value, $group]) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }
    }
}
