<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    public static function legalPages(): array
    {
        return [
            '/kunye' => 'Künye',
            '/bizeulasin' => 'İletişim',
            '/bizeulasin/izleyicitemsilcisi' => 'İzleyici Temsilcisi',
            '/kullanimkosullari' => 'Kullanım Koşulları',
            '/aydinlatma-metni' => 'Aydınlatma Metni',
            '/cerez-politikasi' => 'Çerez Politikası',
            '/privacy' => 'Kişisel Verilerin Korunması Politikası',
            '/biyografi' => 'Biyografi',
        ];
    }

    public function test_each_legal_page_renders_with_self_canonical_and_webpage_jsonld(): void
    {
        foreach (self::legalPages() as $path => $title) {
            $response = $this->get($path)->assertOk();

            $response->assertSee($title, false);
            $response->assertSee('<link rel="canonical" href="'.url($path).'">', false);
            $response->assertSee('og:image:width" content="180"', false);
            $response->assertSee('"@type":"WebPage"', false);
            $response->assertSee('BreadcrumbList', false);
        }
    }

    public function test_contact_page_has_form_fields_and_subjects(): void
    {
        $response = $this->get('/bizeulasin')->assertOk();

        foreach (['Seçiniz', 'Ad Soyad', 'Eposta', 'Telefon', 'Konu', 'Mesaj', 'Mesajı Gönder'] as $label) {
            $response->assertSee($label, false);
        }

        foreach (['Öneri', 'İstek', 'Şikayet', 'Teşekkür', 'Yardım'] as $subject) {
            $response->assertSee($subject, false);
        }
    }

    public function test_contact_form_validates_and_confirms_submission(): void
    {
        $this->post('/bizeulasin', [])->assertSessionHasErrors(['type', 'name', 'email', 'subject', 'message']);

        $this->post('/bizeulasin', [
            'type' => 'Öneri',
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse@example.com',
            'phone' => '+90 555 000 0000',
            'subject' => 'Yayın akışı',
            'message' => 'Program bilgisi rica ederim.',
        ])->assertRedirect()->assertSessionHas('contact_status');
    }

    public function test_footer_exposes_all_legal_links(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach (array_keys(self::legalPages()) as $path) {
            $this->assertStringContainsString($path, $html, "Footer'da eksik yasal link: {$path}");
        }
    }
}
