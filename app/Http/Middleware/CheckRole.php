<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Si aucun rôle n'est spécifié ou si le rôle de l'utilisateur correspond à l'un des rôles autorisés
        if (empty($roles) || in_array(Auth::user()->role->nom, $roles)) {
            if (Auth::user()->statut === 'actif') {
                return $next($request);
            } else {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Votre compte est en attente d\'approbation par un administrateur.');
            }
        }

        return redirect()->route('login')
            ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
    }
}
