<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin panel erişimi: aktif admin/editor kullanıcıları kabul eder.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->is_active || ! ($user->isAdmin() || $user->isEditor())) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}