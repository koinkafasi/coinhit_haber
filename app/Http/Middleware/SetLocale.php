<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * URL prefix'inden aktif locale'i belirler ve uygular (master spec §18.3).
 *
 * Bilinmeyen bir dil prefix'i (`/de/...`) 404 döner; tr prefixsizdir.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Locale::detect($request);

        abort_if($locale === null, 404);

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        return $next($request);
    }
}
