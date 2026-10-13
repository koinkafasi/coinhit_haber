<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Services\Media\ImageProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdvertisementAdminController extends Controller
{
    public function __construct(private readonly ImageProcessor $images) {}

    public function index(): View
    {
        return view('admin.advertisements.index', [
            'ads' => Advertisement::orderByDesc('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.advertisements.form', [
            'ad' => new Advertisement(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Advertisement::create($this->validated($request));

        return redirect()->route('admin.advertisements.index')->with('status', 'Reklam oluşturuldu.');
    }

    public function edit(Advertisement $advertisement): View
    {
        return view('admin.advertisements.form', ['ad' => $advertisement]);
    }

    public function update(Request $request, Advertisement $advertisement): RedirectResponse
    {
        $advertisement->update($this->validated($request, $advertisement));

        return redirect()->route('admin.advertisements.index')->with('status', 'Reklam güncellendi.');
    }

    public function destroy(Advertisement $advertisement): RedirectResponse
    {
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')->with('status', 'Reklam silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Advertisement $ad = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'position' => ['required', Rule::in(array_keys(Advertisement::zones()))],
            'link_url' => ['nullable', 'url', 'max:500'],
            'advertiser_name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['code'] = filled($validated['code'] ?? null) ? (string) $validated['code'] : null;
        $validated['starts_at'] = filled($validated['starts_at'] ?? null) ? $validated['starts_at'] : null;
        $validated['ends_at'] = filled($validated['ends_at'] ?? null) ? $validated['ends_at'] : null;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->images->store($request->file('image'), 'ads');
        }

        return $validated;
    }
}
