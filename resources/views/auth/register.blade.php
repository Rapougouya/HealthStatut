@extends('layouts.auth')

@section('title', 'HealthStatut - Inscription')

@section('content')
<div class="login-container">
  <div class="login-left">
    <div class="login-logo">
      <h1>Health<span>Statut</span></h1>
    </div>
    <div class="illustration">
      <img src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png" 
           alt="Healthcare Illustration" 
           class="fade-in"
           style="width: 250px; height: auto;">
    </div>
    <div class="login-info">
      <h2 class="slide-in">Plateforme de surveillance médicale</h2>
      <p class="slide-in delay-200">Rejoignez notre plateforme pour surveiller les données vitales de vos patients en temps réel</p>
    </div>
  </div>

  <div class="login-right">
    <div class="login-form-container">
      <h2 class="welcome-text">Inscription</h2>
      <p class="login-subtitle">Créez votre compte sur HealthStatut</p>
      
      @if(session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif
      
      <form id="registerForm" class="login-form" method="POST" action="{{ route('register') }}">
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
                <input type="email" id="email" name="email" placeholder="Entrez votre email" required
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
                <input type="password" id="password" name="password" placeholder="Créez un mot de passe" required
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
                <select id="service_id" name="service_id" class="@error('service_id') is-invalid @enderror">
                  <option value="">Sélectionnez votre service</option>
                  @foreach(\App\Models\Service::all() as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                      {{ $service->nom }}
                    </option>
                  @endforeach
                </select>
              </div>
              @error('service_id')
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
        
        <button type="submit" class="login-button pulse">
          S'inscrire
          <span class="btn-arrow"></span>
        </button>
      </form>
      
      <div class="register-link">
        Vous avez déjà un compte? <a href="{{ route('login') }}">Connectez-vous</a>
      </div>
      
      <div class="login-footer">
        <p>©{{ date('Y') }} HealthStatut pour plus d'informations.<br>Contactez-nous à <a href="mailto:care@healthmobis.com">care@healthmobis.com</a></p>
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
  
  // Toggle password visibility pour le mot de passe
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

  // Toggle password visibility pour la confirmation du mot de passe
  const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
  const passwordConfirmInput = document.getElementById('password_confirmation');
  
  if (togglePasswordConfirm && passwordConfirmInput) {
    togglePasswordConfirm.addEventListener('click', function() {
      const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordConfirmInput.setAttribute('type', type);
      
      // Change icon based on password visibility
      if (type === 'text') {
        togglePasswordConfirm.querySelector('.eye-icon').classList.add('eye-off');
      } else {
        togglePasswordConfirm.querySelector('.eye-icon').classList.remove('eye-off');
      }
    });
  }
  
  // Form validation feedback
  const registerForm = document.getElementById('registerForm');
  const inputs = registerForm.querySelectorAll('input[required]');
  
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

  // Vérification de la concordance des mots de passe
  const password = document.getElementById('password');
  const passwordConfirm = document.getElementById('password_confirmation');
  
  passwordConfirm.addEventListener('input', function() {
    if (password.value !== this.value) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
    }
  });
});
</script>
@endsection
