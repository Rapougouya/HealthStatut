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
  
  if (loginForm) {
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
    
    // Simple form validation before submission
    loginForm.addEventListener('submit', function(event) {
      let hasError = false;
      
      inputs.forEach(input => {
        if (input.value.trim() === '') {
          input.classList.add('is-invalid');
          hasError = true;
        }
      });
      
      if (hasError) {
        event.preventDefault();
        // Afficher un message d'erreur général si nécessaire
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
          errorMessage.textContent = 'Veuillez remplir tous les champs requis.';
          errorMessage.style.display = 'block';
        }
      }
    });
  }
  
  // Pour le formulaire d'inscription
  const registerForm = document.getElementById('registerForm');
  
  if (registerForm) {
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
    
    if (password && passwordConfirm) {
      passwordConfirm.addEventListener('input', function() {
        if (password.value !== this.value) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
        } else {
          this.classList.remove('is-invalid');
          this.classList.add('is-valid');
        }
      });
    }
    
    // Toggle password visibility pour le formulaire d'inscription
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    
    if (togglePasswordConfirm && passwordConfirm) {
      togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirm.setAttribute('type', type);
        
        // Change icon based on password visibility
        if (type === 'text') {
          togglePasswordConfirm.querySelector('.eye-icon').classList.add('eye-off');
        } else {
          togglePasswordConfirm.querySelector('.eye-icon').classList.remove('eye-off');
        }
      });
    }
  }
});
