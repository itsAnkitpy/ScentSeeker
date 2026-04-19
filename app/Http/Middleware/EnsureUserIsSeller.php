<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->isSeller()) {
            Filament::auth()->logout();

            return redirect(Filament::getLoginUrl());
        }

        $status = $user->seller?->onboarding_status;

        if ($status !== 'active') {
            Filament::auth()->logout();

            $request->session()->flash(
                'error',
                $status === 'suspended'
                    ? 'Your seller account is suspended. Please contact support.'
                    : 'Your seller account is not yet active. Please contact support.'
            );

            return redirect(Filament::getLoginUrl());
        }

        return $next($request);
    }
}
