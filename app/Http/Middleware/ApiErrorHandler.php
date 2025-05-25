<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

class ApiErrorHandler
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ressource non trouvée',
                'error' => 'NOT_FOUND'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
                'error' => 'VALIDATION_ERROR'
            ], 422);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
                'error' => 'UNAUTHENTICATED'
            ], 401);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
                'error' => 'UNAUTHORIZED'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->environment('production') 
                    ? 'Erreur interne du serveur' 
                    : $e->getMessage(),
                'error' => 'INTERNAL_ERROR'
            ], 500);
        }
    }
}