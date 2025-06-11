<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Gère une demande de connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentative de connexion avec l'email comme 'username'
        $credentials = [
            'email' => $request->username,
            'password' => $request->password
        ];
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Vérification du statut du compte
            $user = Auth::user();
            if ($user->statut === 'inactif') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Votre compte est en attente d\'approbation par un administrateur.',
                ])->withInput($request->except('password'));
            }
            
            User::where('id', Auth::id())->update([
                'derniere_connexion' => now(),
            ]);

            $request->session()->regenerate();
            
            // Redirection en fonction du rôle
            if ($user->role->nom === 'admin') {
                return redirect()->route('dashboard');
            } elseif ($user->role->nom === 'medecin') {
                return redirect()->route('patients.index');
            } elseif ($user->role->nom === 'patient') {
                return redirect()->route('dashboard');
            } elseif ($user->role->nom === 'technicien') {
                return redirect()->route('sensors.index');
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'username' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->except('password'));
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Affiche le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Gère une demande d'inscription
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'service_id' => 'required|exists:services,id',
            'role_id' => 'required|exists:roles,id',
            'accept_terms' => 'required',
        ]);

        // Vérifier que le rôle n'est pas admin (sécurité supplémentaire)
        $role = Role::findOrFail($request->role_id);
        if ($role->nom === 'admin') {
            return back()->withErrors(['role_id' => 'Ce type de compte ne peut pas être sélectionné.'])->withInput();
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'service_id' => $request->service_id,
            'role_id' => $request->role_id,
            'statut' => 'inactif', // Le compte doit être approuvé par un administrateur
        ]);

        // Message spécifique selon le rôle choisi
        $roleName = $role->nom;
        $message = 'Votre compte a été créé avec succès et est en attente d\'approbation par un administrateur.';
        
        if ($roleName === 'patient') {
            $message .= ' Un médecin devra vous assigner comme patient.';
        } elseif ($roleName === 'medecin') {
            $message .= ' Vous pourrez accéder aux dossiers patients après approbation.';
        } elseif ($roleName === 'technicien') {
            $message .= ' Vous pourrez accéder à la gestion des capteurs après approbation.';
        }

        // Redirection vers la page de connexion avec un message de succès
        return redirect()->route('login')
            ->with('success', $message);
    }

    /**
     * Affiche le formulaire de demande de réinitialisation de mot de passe
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envoie un lien de réinitialisation du mot de passe par email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Affiche le formulaire de réinitialisation du mot de passe
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Réinitialise le mot de passe
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
