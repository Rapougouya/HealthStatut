<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) return redirect('/login');

        $user = Auth::user();

        // Vérifie que l'utilisateur a bien un rôle
        if ($user->role && in_array($user->role->nom, $roles)) {
            return $next($request);
        }

        abort(403, 'Accès refusé. Vous n’avez pas l’autorisation.');
    }
}
