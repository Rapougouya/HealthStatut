<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParametreController extends Controller
{
    /**
     * Affiche la page des paramètres utilisateur
     */
    public function index()
    {
        return view('parametres.index');
    }
    
    /**
     * Affiche la page d'administration générale
     */
    public function adminIndex()
    {
        $users = User::with(['role', 'services'])->get();
        $services = Service::all();
        $roles = Role::all();
        
        return view('admin.parametres', compact('users', 'services', 'roles'));
    }

    /**
     * Met à jour le profil de l'utilisateur connecté
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Traitement de l'avatar si présent
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }
        
        User::where('id', $user->id)->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'bio' => $request->bio,
            'avatar' => $user->avatar
        ]);
        
        return redirect()->route('parametres.index')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Met à jour les seuils d'alerte pour les patients
     */
    public function updateThresholds(Request $request)
    {
        // Logique pour mettre à jour les seuils d'alerte
        return redirect()->back()->with('success', 'Seuils d\'alerte mis à jour avec succès.');
    }

    /**
     * Remet à zéro les seuils d'alerte pour les patients
     */
    public function resetThresholds(Request $request)
    {
        // Logique pour remettre à zéro les seuils d'alerte
        return redirect()->back()->with('success', 'Seuils d\'alerte remis à zéro avec succès.');
    }

    /**
     * Affiche les paramètres du patient
     */
    public function patientSettings()
    {
        return view('parametres.patient-settings');
    }

    /**
     * Met à jour le profil du patient
     */
    public function updatePatientProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
        ]);

        User::where('id', $user->id)->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);
        
        return redirect()->route('patient.parametres')->with('success', 'Profil mis à jour avec succès.');
    }
}