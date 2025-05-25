@extends('layouts.auth')

@section('title', 'HealthStatut - Connexion')

@section('content')
<div class="login-container">
  <div class="login-left">
    <div class="login-logo">
      <h1>Health<span>STATUT</span></h1>
    </div>
    <div class="illustration">
      <img src="{{asset('images/logo_ministere_sante.png')}}" alt="Logo" class="fade-in">
    </div>
    <div class="login-info">
      <h2 class="slide-in">Plateforme de surveillance médicale</h2>
      <p class="slide-in delay-200">Surveillez les données vitales de vos patients en temps réel</p>
    </div>
  </div>

  <div class="login-right">
    <div class="login-form-container">
      <h2 class="welcome-text">Bienvenue</h2>
      <p class="login-subtitle">Accédez à votre espace de surveillance</p>
      
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif
      
      <form id="loginForm" class="login-form" method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
          <label for="username">Identifiant</label>
          <div class="input-with-icon">
            <span class="input-icon user-icon"></span>
            <input type="text" id="username" name="username" placeholder="Entrez votre email" required
                  value="{{ old('username') }}" class="@error('username') is-invalid @enderror">
          </div>
          @error('username')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="input-with-icon password-input">
            <span class="input-icon lock-icon"></span>
            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required
                  class="@error('password') is-invalid @enderror">
            <button type="button" id="togglePassword" class="toggle-password">
              <span class="eye-icon"></span>
            </button>
          </div>
          @error('password')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        
        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="checkmark"></span>
            Se souvenir de moi
          </label>
          <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié?</a>
        </div>
        
        <button type="submit" class="login-button pulse">
          Se connecter
          <span class="btn-arrow"></span>
        </button>
      </form>
      
      <div class="login-link">
        Vous n'avez pas de compte? <a href="{{ route('register') }}">Inscrivez-vous</a>
      </div>
      
      <div class="login-footer">
        <p>©{{ date('Y') }} HealthStatut pour plus d'informations.<br>Contactez-nous à <a href="mailto:care@healthstatut.com">care@healthstatut.com</a></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Animation d'entrée des éléments
  const animateElements = document.querySelectorAll('.fade-in, .slide-in');
  animateElements.forEach(element => {
    element.classList.add('visible');
  });
  
  // Toggle password visibility
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Change icon based on password visibility
      if (type === 'text') {
        togglePassword.querySelector('.eye-icon').classList.add('eye-off');
      } else {
        togglePassword.querySelector('.eye-icon').classList.remove('eye-off');
      }
    });
  }
  
  // Form validation feedback
  const loginForm = document.getElementById('loginForm');
  const inputs = loginForm.querySelectorAll('input[required]');
  
  inputs.forEach(input => {
    input.addEventListener('blur', function() {
      if (this.value.trim() === '') {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      }
    });
    
    input.addEventListener('input', function() {
      if (this.value.trim() !== '') {
        this.classList.remove('is-invalid');
      }
    });
  });
});
</script>
@endsection