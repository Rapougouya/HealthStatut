@extends('layouts.auth')

@section('title', 'HealthStatut - Inscription')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-container">
  <div class="register-left">
    <div class="register-logo">
      <img src="{{ asset('images/logo_ministere_sante.png') }}" alt="HealthStatut Logo" class="fade-in">
      <h1>Health<span>STATUT</span></h1>
    </div>
    
    <div class="bg-pattern"></div>
    
    <div class="register-info">
      <h2 class="slide-in">Plateforme de surveillance médicale</h2>
      <p class="slide-in delay-200">Rejoignez notre plateforme innovante pour la surveillance des données vitales en temps réel.</p>
      
      <ul class="benefits-list">
        <li>Surveillance des patients en temps réel</li>
        <li>Analyse des données vitales</li>
        <li>Alertes et notifications instantanées</li>
        <li>Collaboration entre professionnels de santé</li>
      </ul>
    </div>
  </div>

  <div class="register-right">
    <div class="register-form-container">
      <h2 class="welcome-text">Inscription</h2>
      <p class="register-subtitle">Créez votre compte professionnel sur HealthStatut</p>
      
      @if(session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif
      
      <form id="registerForm" class="register-form" method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="form-row">
          <div class="form-column">
            <div class="form-group">
              <label for="nom">Nom</label>
              <div class="input-with-icon">
                <span class="input-icon user-icon"></span>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required
                      value="{{ old('nom') }}" class="@error('nom') is-invalid @enderror">
              </div>
              @error('nom')
                <span class="error-message">{{ $message }}</span>
              @enderror
            </div>
            
            <div class="form-group">
              <label for="email">Email</label>
              <div class="input-with-icon">
                <span class="input-icon email-icon"></span>
                <input type="email" id="email" name="email" placeholder="Entrez votre email professionnel" required
                      value="{{ old('email') }}" class="@error('email') is-invalid @enderror">
              </div>
              @error('email')
                <span class="error-message">{{ $message }}</span>
              @enderror
            </div>
            
            <div class="form-group">
              <label for="password">Mot de passe</label>
              <div class="input-with-icon password-input">
                <span class="input-icon lock-icon"></span>
                <input type="password" id="password" name="password" placeholder="Créez un mot de passe sécurisé" required
                      class="@error('password') is-invalid @enderror">
                <button type="button" id="togglePassword" class="toggle-password">
                  <span class="eye-icon"></span>
                </button>
              </div>
              @error('password')
                <span class="error-message">{{ $message }}</span>
              @enderror
            </div>
          </div>
          
          <div class="form-column">
            <div class="form-group">
              <label for="prenom">Prénom</label>
              <div class="input-with-icon">
                <span class="input-icon user-icon"></span>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required
                      value="{{ old('prenom') }}" class="@error('prenom') is-invalid @enderror">
              </div>
              @error('prenom')
                <span class="error-message">{{ $message }}</span>
              @enderror
            </div>
            
            <div class="form-group">
              <label for="service_id">Service</label>
              <div class="input-with-icon">
                <span class="input-icon service-icon"></span>
                <select id="service_id" name="service_id" class="@error('service_id') is-invalid @enderror" required>
                  <option value="">Sélectionnez votre service</option>
                  @foreach(App\Models\Service::all() as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                      {{ $service->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              @error('service_id')
                <span class="error-message">{{ $message }}</span>
              @enderror
            </div>
            
            <div class="form-group">
              <label for="role_id">Type de compte</label>
              <div class="input-with-icon">
                <span class="input-icon user-icon"></span>
                <select id="role_id" name="role_id" class="@error('role_id') is-invalid @enderror" required>
                  <option value="">Sélectionnez votre rôle</option>
                  @foreach(App\Models\Role::where('nom', '!=', 'admin')->get() as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                      {{ ucfirst($role->nom) }}
                    </option>
                  @endforeach
                </select>
              </div>
              @error('role_id')
                <span class="error-message">{{ $message }}</span>
              @enderror
            </div>
            
            <div class="form-group">
              <label for="password_confirmation">Confirmer le mot de passe</label>
              <div class="input-with-icon password-input">
                <span class="input-icon lock-icon"></span>
                <input type="password" id="password_confirmation" name="password_confirmation" 
                       placeholder="Confirmez votre mot de passe" required>
                <button type="button" id="togglePasswordConfirm" class="toggle-password">
                  <span class="eye-icon"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="terms-checkbox">
          <label class="remember-me">
            <input type="checkbox" id="accept_terms" name="accept_terms" required>
            <span class="checkmark"></span>
            J'accepte les conditions d'utilisation et la politique de confidentialité
          </label>
        </div>
        
        <button type="submit" class="register-button pulse">
          Créer mon compte
          <span class="btn-arrow"></span>
        </button>
      </form>
      
      <div class="login-link">
        Vous avez déjà un compte? <a href="{{ route('login') }}">Connectez-vous</a>
      </div>
      
      <div class="login-footer">
        <p>©{{ date('Y') }} HealthStatut - Ministère de la Santé<br>Pour plus d'informations, contactez-nous à <a href="mailto:support@healthstatut.ma">support@healthstatut.ma</a></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/register.js') }}"></script>
@endsection
