<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Service;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->input('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $role = $user->role->nom ?? null;

            // Redirection selon le rôle avec les routes existantes
            switch ($role) {
                case 'admin':
                    return redirect()->route('dashboard')
                        ->with('success', 'Bienvenue Administrateur');
                
                case 'medecin':
                    return redirect()->route('alerts.index')
                        ->with('success', 'Bienvenue Docteur');
                
                case 'technicien':
                    return redirect()->route('sensors.index')
                        ->with('success', 'Bienvenue Technicien');
                
                case 'patient':
                    return redirect()->route('patients.index')
                        ->with('success', 'Bienvenue Patient');
                
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Identifiants incorrects']);
    }

    public function showRegistrationForm()
    {
        $roles = Role::where('id', '!=', 1)->get(); // Exclure le rôle admin
        $services = Service::all();

        return view('auth.register', compact('roles', 'services'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'service_id' => 'nullable|exists:services,id'
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'service_id' => $request->service_id,
            'statut' => 'actif'
        ]);

        // Connecte automatiquement si nécessaire
        if ($request->auto_login) {
            Auth::login($user);
            return $this->redirectToDashboard();
        }

        return redirect()->route('login')
            ->with('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
    }

    protected function redirectToDashboard()
    {
        $user = Auth::user();
        $role = $user->role->nom ?? null;

        switch ($role) {
            case 'admin': return redirect()->route('dashboard');
            case 'medecin': return redirect()->route('alerts.index');
            case 'technicien': return redirect()->route('sensors.index');
            case 'patient': return redirect()->route('patients.show', $user->id);
            default: return redirect()->route('dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}