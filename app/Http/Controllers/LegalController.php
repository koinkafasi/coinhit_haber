<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Yasal statik sayfalar (master spec §3, §11.6, §14.4).
 *
 * İletişim, Künye, Biyografi, İzleyici Temsilcisi, Kullanım Koşulları,
 * Aydınlatma Metni, Çerez Politikası ve KVKK politikası.
 */
class LegalController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const CONTACT_SUBJECT_KEYS = [
        'content.contact_subject_suggestion',
        'content.contact_subject_request',
        'content.contact_subject_complaint',
        'content.contact_subject_thanks',
        'content.contact_subject_help',
    ];

    public function show(string $slug): View
    {
        $pages = __('legal.pages');

        abort_unless(array_key_exists($slug, $pages), 404);

        $page = $pages[$slug];

        return view('legal.show', array_merge([
            'page' => $page,
            'slug' => $slug,
            'company' => config('legal.company', []),
            'pageTitle' => $page['title'].' | Bloomberght',
            'pageDescription' => $page['description'] ?? '',
            'pageType' => 'legal',
            'contactSubjects' => array_map(static fn (string $key): string => __($key), self::CONTACT_SUBJECT_KEYS),
            'breadcrumbs' => [
                ['label' => $page['title'], 'url' => \App\Support\Locale::url('/'.$slug)],
            ],
        ], $this->layoutData()));
    }

    /**
     * İletişim formu gönderimi (Bölüm 14.4). Kalıcı veri tutulmaz; teyit döner.
     */
    public function contact(): RedirectResponse
    {
        request()->validate([
            'type' => ['required', 'string'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        return back()->with('contact_status', __('content.contact_status_message'));
    }
}
