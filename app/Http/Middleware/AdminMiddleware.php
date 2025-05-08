<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()?->role?->name !== 'Administrateur') {
            return redirect('/')->with('error', 'Accès non autorisé');
        }

        return $next($request);
    }
}