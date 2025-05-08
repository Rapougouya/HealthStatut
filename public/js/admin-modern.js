/**
 * AdminPanel.js pour Laravel
 * Version adaptée pour l'intégration avec Laravel
 */

// Import du script principal
document.write('<script src="' + window.location.origin + '/src/scripts/admin-modern.js"></script>');

// Fonctions spécifiques pour l'intégration Laravel
document.addEventListener('DOMContentLoaded', function() {
  // Intégration avec les messages flash de Laravel
  const flashMessages = document.querySelectorAll('.alert');
  
  flashMessages.forEach(message => {
    if (message.classList.contains('alert-success')) {
      showAdminToast(message.textContent, 'success');
    } else if (message.classList.contains('alert-danger')) {
      showAdminToast(message.textContent, 'error');
    } else if (message.classList.contains('alert-warning')) {
      showAdminToast(message.textContent, 'warning');
    } else if (message.classList.contains('alert-info')) {
      showAdminToast(message.textContent, 'info');
    }
    
    // Masquer le message flash d'origine
    message.style.display = 'none';
  });
  
  // Fonction d'affichage de toast (si le script principal n'est pas encore chargé)
  function showAdminToast(message, type = 'info', duration = 5000) {
    // Attendre que la classe AdminPanel soit disponible
    if (typeof window.adminPanel === 'undefined') {
      setTimeout(() => {
        showAdminToast(message, type, duration);
      }, 100);
      return;
    }
    
    // Utiliser la méthode de notification de AdminPanel
    window.adminPanel.showToast(message, type, duration);
  }
  
  // Intégration avec les formulaires Laravel
  document.querySelectorAll('form').forEach(form => {
    // Si le formulaire est désactivé pour la validation AJAX, ne pas interférer
    if (form.dataset.ajax === 'false') return;
    
    // Ajouter la gestion AJAX par défaut
    form.dataset.ajax = 'true';
    
    // Ajouter le header X-Requested-With pour les requêtes AJAX
    const submitHandler = form.onsubmit;
    form.onsubmit = function(e) {
      // Si c'est un formulaire de recherche simple, ne pas interférer
      if (form.classList.contains('search-form')) return;
      
      // Ajouter le header X-Requested-With
      const xhr = new XMLHttpRequest();
      xhr.open(form.method, form.action);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      
      // Si submitHandler existe et retourne false, ne pas soumettre le formulaire
      if (submitHandler && !submitHandler(e)) {
        e.preventDefault();
        return false;
      }
      
      // Laisser la classe AdminPanel gérer la soumission
    };
  });
  
  // Activation des tooltips et popovers
  if (typeof bootstrap !== 'undefined') {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });
  }
  
  // Gestion des onglets avec animation
  const tabLinks = document.querySelectorAll('.nav-link[data-toggle="tab"]');
  tabLinks.forEach(tabLink => {
    tabLink.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Désactiver tous les onglets actifs
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      
      // Activer l'onglet cliqué
      this.classList.add('active');
      
      // Récupérer la cible de l'onglet
      const target = document.querySelector(this.getAttribute('href'));
      
      // Masquer tous les contenus d'onglets avec animation
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('show');
        content.classList.add('fade-transition');
        content.style.display = 'none';
      });
      
      // Afficher le contenu de l'onglet sélectionné avec animation
      if (target) {
        target.style.display = 'block';
        setTimeout(() => {
          target.classList.add('show');
        }, 10);
      }
    });
  });
  
  // Initialiser l'onglet actif
  const activeTab = document.querySelector('.nav-link.active[data-toggle="tab"]');
  if (activeTab) {
    const target = document.querySelector(activeTab.getAttribute('href'));
    if (target) {
      target.style.display = 'block';
      target.classList.add('show');
    }
  }
});