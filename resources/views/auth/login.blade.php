@extends('layouts.auth')

@section('title', 'HealthStatut - Connexion')

@section('content')
<div class="login-container">
  <div class="login-left">
    <div class="login-logo">
      <h1>Health<span>Statut</span></h1>
    </div>
    <div class="illustration">
      <img src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png" alt="Healthcare Illustration">
    </div>
    <div class="login-info">
      <h2>Plateforme de gestion des patients</h2>
      <p>Surveillez les données vitales de vos patients en temps réel</p>
    </div>
  </div>
  <div class="login-right">
    <div class="login-form-container">
      <h2>Connexion</h2>
      <p class="login-subtitle">Accédez à votre espace de surveillance</p>
      
      <form id="loginForm" class="login-form" method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
          <label for="email">Identifiant</label>
          <input type="text" id="email" name="email" placeholder="Entrez votre identifiant" required
                 value="{{ old('email') }}" class="@error('email') is-invalid @enderror">
          @error('email')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="password-input">
            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required
                   class="@error('password') is-invalid @enderror">
            <button type="button" id="togglePassword" class="toggle-password">
              <i class="icon-eye"></i>
            </button>
          </div>
          @error('password')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div id="error-message" style="color: red; display: none;"></div>
        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="checkmark"></span>
            Se souvenir de moi
          </label>
          <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié?</a>
        </div>
        
        <button type="submit" class="login-button">Se connecter</button>
      </form>
      <p class="register-link">
        Vous n'avez pas de compte ? <a href="{{ route('register') }}">S'inscrire</a>
      </p>
      <div class="login-footer">
        <p>©{{ date('Y') }} HealthStatut pour plus d'informations.<br>Contactez-nous à care@healthstatut.com</p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/login.js') }}"></script>
@endsection