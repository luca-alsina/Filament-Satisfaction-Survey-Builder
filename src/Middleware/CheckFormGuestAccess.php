<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Luca\FilamentSatisfactionSurveyBuilder\Models\FilamentSurveyForm;

class CheckFormGuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated, allow access
        if (Auth::check()) {
            return $next($request);
        }

        $form = $request->route('form');

        // If form allows guest entries, allow access
        if ($form instanceof FilamentSurveyForm && $form->permit_guest_entries) {
            return $next($request);
        }

        // Otherwise, redirect to login
        return redirect()->guest(route('login'));
    }
}
