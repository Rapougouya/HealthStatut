<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParametreController extends Controller
{
    /**
     * Affiche la page des paramètres
     */
    public function index()
    {
        $users = User::with(['role', 'service'])->get();
        $services = Service::all();
        $roles = Role::all();
        
        return view('admin.parametres', compact('users', 'services', 'roles'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
            'service_id' => 'required|exists:services,id',
        ]);

        // Génération d'un mot de passe aléatoire
        $password = Str::random(10);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role_id' => $request->role_id,
            'service_id' => $request->service_id,
            'statut' => 'actif',
        ]);

        // Ici, vous pourriez envoyer un e-mail au nouvel utilisateur avec son mot de passe

        return redirect()->route('parametres.index')->with('success', 'Utilisateur créé avec succès. Mot de passe temporaire: ' . $password);
    }

    /**
     * Met à jour un utilisateur existant
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'service_id' => $request->service_id,
        ]);

        return redirect()->route('parametres.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function toggleUserStatus(User $user)
    {
        $user->statut = $user->statut === 'actif' ? 'inactif' : 'actif';
        $user->save();

        $status = $user->statut === 'actif' ? 'activé' : 'désactivé';
        return redirect()->route('parametres.index')->with('success', "Utilisateur $status avec succès.");
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur
     */
    public function resetUserPassword(User $user)
    {
        // Génération d'un mot de passe aléatoire
        $password = Str::random(10);
        
        $user->update([
            'password' => Hash::make($password),
        ]);

        // Ici, vous pourriez envoyer un e-mail à l'utilisateur avec son nouveau mot de passe

        return redirect()->route('parametres.index')->with('success', 'Mot de passe réinitialisé avec succès. Nouveau mot de passe: ' . $password);
    }

    /**
     * Enregistre un nouveau service
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('parametres.index')->with('success', 'Service créé avec succès.');
    }

    /**
     * Met à jour un service existant
     */
    public function updateService(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('parametres.index')->with('success', 'Service mis à jour avec succès.');
    }
}