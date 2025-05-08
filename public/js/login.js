document.addEventListener('DOMContentLoaded', function() {
  // Toggle password visibility
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Change icon based on password visibility
      if (type === 'text') {
        togglePassword.innerHTML = '<i class="icon-eye" style="opacity: 0.7;"></i>';
      } else {
        togglePassword.innerHTML = '<i class="icon-eye"></i>';
      }
    });
  }
  
  // Login form submission
  const loginForm = document.getElementById('loginForm');
  
  if (loginForm) {
    loginForm.addEventListener('submit', function(event) {
      event.preventDefault();
      
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const remember = document.getElementById('remember').checked;
      
      // Envoyer les données du formulaire via AJAX
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Redirection après une connexion réussie
          window.location.href = '/'; // Remplacez par votre URL de tableau de bord
        } else {
          // Afficher un message d'erreur
          alert(data.message || 'Échec de la connexion');
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite lors de la connexion');
      });
    });
  }
});