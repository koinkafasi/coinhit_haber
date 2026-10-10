<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KapDisclosure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KapAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.kap.index', [
            'disclosures' => KapDisclosure::orderByDesc('published_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.kap.form', [
            'disclosure' => new KapDisclosure(['published_at' => now()]),
            'dictionary' => KapDisclosure::FIELD_DICTIONARY,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $disclosure = KapDisclosure::create($this->validated($request));
        $this->syncFields($request, $disclosure);

        return redirect()->route('admin.kap.edit', $disclosure)->with('status', 'KAP bildirimi oluşturuldu.');
    }

    public function edit(KapDisclosure $kap): View
    {
        return view('admin.kap.form', [
            'disclosure' => $kap->load('fields'),
            'dictionary' => KapDisclosure::FIELD_DICTIONARY,
        ]);
    }

    public function update(Request $request, KapDisclosure $kap): RedirectResponse
    {
        $kap->update($this->validated($request, $kap));
        $this->syncFields($request, $kap);

        return redirect()->route('admin.kap.edit', $kap)->with('status', 'KAP bildirimi güncellendi.');
    }

    public function destroy(KapDisclosure $kap): RedirectResponse
    {
        $kap->delete();

        return redirect()->route('admin.kap.index')->with('status', 'KAP bildirimi silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?KapDisclosure $kap = null): array
    {
        $validated = $request->validate([
            'kap_id' => ['nullable', 'integer'],
            'company_code' => ['required', 'string', 'max:20'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_slug' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['company_slug'] = filled($validated['company_slug'] ?? null)
            ? Str::slug($validated['company_slug'])
            : Str::slug($validated['company_name']);
        $validated['kap_id'] = filled($validated['kap_id'] ?? null)
            ? (int) $validated['kap_id']
            : (($kap?->kap_id) ?? ((int) KapDisclosure::max('kap_id')) + 1);

        return $validated;
    }

    private function syncFields(Request $request, KapDisclosure $disclosure): void
    {
        $fields = (array) $request->input('fields', []);

        foreach (KapDisclosure::FIELD_DICTIONARY as $key => [$labelTr, $labelEn]) {
            $values = (array) ($fields[$key] ?? []);
            $valueTr = filled($values['tr'] ?? null) ? (string) $values['tr'] : null;
            $valueEn = filled($values['en'] ?? null) ? (string) $values['en'] : null;

            if ($valueTr === null && $valueEn === null) {
                continue;
            }

            $disclosure->fields()->updateOrCreate(
                ['key' => $key],
                [
                    'label_tr' => $labelTr,
                    'label_en' => $labelEn,
                    'value_tr' => $valueTr,
                    'value_en' => $valueEn,
                ]
            );
        }
    }
}