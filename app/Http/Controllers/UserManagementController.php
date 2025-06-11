<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    /**
     * Affiche la page d'administration des utilisateurs
     */
    public function index()
    {
        $users = User::with(['role', 'services'])->get();
        $services = Service::all();
        $roles = Role::all();
        
        // Calculs pour les statistiques
        $totalUsers = $users->count();
        $activeUsers = $users->where('statut', 'actif')->count();
        $inactiveUsers = $users->where('statut', 'inactif')->count();
        $adminUsers = $users->filter(function($user) {
            return $user->role && $user->role->nom === 'admin';
        })->count();
        
        return view('admin.users.index', compact('users', 'services', 'roles', 'totalUsers', 'activeUsers', 'inactiveUsers', 'adminUsers'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
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

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès. Mot de passe temporaire: ' . $password);
    }

    /**
     * Met à jour un utilisateur existant
     */
    public function update(Request $request, User $user)
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

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function toggleStatus(User $user)
    {
        $user->statut = $user->statut === 'actif' ? 'inactif' : 'actif';
        $user->save();

        $status = $user->statut === 'actif' ? 'activé' : 'désactivé';
        return redirect()->route('utilisateurs.index')->with('success', "Utilisateur $status avec succès.");
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur
     */
    public function resetPassword(User $user)
    {
        // Génération d'un mot de passe aléatoire
        $password = Str::random(10);
        
        $user->update([
            'password' => Hash::make($password),
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'Mot de passe réinitialisé avec succès. Nouveau mot de passe: ' . $password);
    }

    /**
     * Met à jour les paramètres de notification
     */
    public function updateNotifications(Request $request)
    {
        // Logique pour mettre à jour les paramètres de notification
        return redirect()->back()->with('success', 'Paramètres de notification mis à jour avec succès.');
    }

    /**
     * Met à jour les paramètres d'apparence
     */
    public function updateAppearance(Request $request)
    {
        // Logique pour mettre à jour les paramètres d'apparence
        return redirect()->back()->with('success', 'Paramètres d\'apparence mis à jour avec succès.');
    }
}
