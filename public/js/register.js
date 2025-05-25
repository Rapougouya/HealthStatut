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
    togglePassword.addEventListener('click', function(e) {
      // Empêcher le comportement par défaut qui pourrait soumettre le formulaire
      e.preventDefault();
      
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
    togglePasswordConfirm.addEventListener('click', function(e) {
      // Empêcher le comportement par défaut
      e.preventDefault();
      
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

  // Sélection de service et rôle
  const serviceSelect = document.getElementById('service_id');
  const roleSelect = document.getElementById('role_id');
  
  [serviceSelect, roleSelect].forEach(select => {
    select.addEventListener('change', function() {
      if (this.value) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
      } else {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      }
    });
  });
  
  // S'assurer que le formulaire peut être soumis
  registerForm.addEventListener('submit', function(event) {
    // La validation HTML5 par défaut gère déjà la soumission
    console.log('Formulaire soumis');
    // Si vous voulez ajouter une validation supplémentaire, vous pouvez le faire ici
  });
});