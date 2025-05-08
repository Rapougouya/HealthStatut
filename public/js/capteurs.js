document.addEventListener('DOMContentLoaded', function() {
    // Notification bell toggle
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBell && notificationDropdown) {
      notificationBell.addEventListener('click', function() {
        notificationDropdown.classList.toggle('show');
      });
      
      // Close notification dropdown when clicking outside
      document.addEventListener('click', function(event) {
        if (!notificationBell.contains(event.target) && !notificationDropdown.contains(event.target)) {
          notificationDropdown.classList.remove('show');
        }
      });
    }
    
    // Mobile menu toggle
    const menuButton = document.querySelector('.menu-button');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuButton && sidebar) {
      menuButton.addEventListener('click', function() {
        sidebar.classList.toggle('show');
      });
    }
    
    // Sensor detail view toggle
    const actionButtons = document.querySelectorAll('[data-sensor]');
    const sensorDetails = document.querySelector('.sensor-details');
    const closeDetailsBtn = document.querySelector('.close-details-btn');
    
    if (actionButtons.length && sensorDetails && closeDetailsBtn) {
      actionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          sensorDetails.classList.add('show');
        });
      });
      
      closeDetailsBtn.addEventListener('click', function() {
        sensorDetails.classList.remove('show');
      });
    }
    
    // Add sensor modal
    const addSensorBtn = document.querySelector('.primary-btn');
    const addSensorModal = document.getElementById('addSensorModal');
    const closeModalBtn = document.querySelector('.close-modal');
    const cancelBtn = document.querySelector('.cancel-btn');
    
    if (addSensorBtn && addSensorModal) {
      addSensorBtn.addEventListener('click', function() {
        addSensorModal.classList.add('show');
      });
      
      if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
          addSensorModal.classList.remove('show');
        });
      }
      
      if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
          addSensorModal.classList.remove('show');
        });
      }
      
      // Close modal when clicking outside
      addSensorModal.addEventListener('click', function(event) {
        if (event.target === addSensorModal) {
          addSensorModal.classList.remove('show');
        }
      });
    }
    
    // Form submission for adding a sensor
    const addSensorForm = document.getElementById('addSensorForm');
    
    if (addSensorForm) {
      addSensorForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Get form values
        const sensorId = document.getElementById('sensorId').value;
        const sensorType = document.getElementById('sensorType').value;
        const sensorModel = document.getElementById('sensorModel').value;
        
        // Create toast notification
        createToast('Capteur ajouté', `Le capteur ${sensorId} a été ajouté avec succès.`, 'success');
        
        // Close the modal
        addSensorModal.classList.remove('show');
        
        // Reset form
        addSensorForm.reset();
      });
    }
    
    // Table row actions
    const tableActionBtns = document.querySelectorAll('.sensors-table .action-btn');
    
    if (tableActionBtns.length) {
      tableActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          if (btn.querySelector('.icon-edit')) {
            createToast('Modification', 'Mode édition activé pour ce capteur.', 'info');
          } else if (btn.querySelector('.icon-configure')) {
            createToast('Configuration', 'Ouverture des paramètres du capteur.', 'info');
          } else if (btn.querySelector('.icon-history')) {
            createToast('Historique', 'Chargement de l\'historique du capteur.', 'info');
          }
        });
      });
    }
    
    // Sensor action buttons in details panel
    const sensorActionBtns = document.querySelectorAll('.sensor-actions .action-button');
    
    if (sensorActionBtns.length) {
      sensorActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          if (btn.classList.contains('primary')) {
            createToast('Modifications enregistrées', 'Les paramètres du capteur ont été mis à jour.', 'success');
          } else if (btn.classList.contains('secondary')) {
            createToast('Réinitialisation', 'Le capteur a été réinitialisé avec succès.', 'warning');
          } else if (btn.classList.contains('warning')) {
            createToast('Patient désassigné', 'Le capteur n\'est plus assigné à aucun patient.', 'warning');
          }
        });
      });
    }
    
    // Toast notification function
    function createToast(title, message, type = 'info') {
      const toastContainer = document.getElementById('toastContainer');
      
      if (!toastContainer) return;
      
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      
      let iconName;
      switch(type) {
        case 'success':
          iconName = 'check-circle';
          break;
        case 'warning':
          iconName = 'alert-triangle';
          break;
        case 'error':
          iconName = 'x-circle';
          break;
        default:
          iconName = 'info';
      }
      
      toast.innerHTML = `
        <div class="toast-icon">
          <i class="icon-${iconName}"></i>
        </div>
        <div class="toast-content">
          <div class="toast-title">${title}</div>
          <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close">&times;</button>
      `;
      
      toastContainer.appendChild(toast);
      
      // Auto remove toast after 5 seconds
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
          toastContainer.removeChild(toast);
        }, 300);
      }, 5000);
      
      // Close button for toast
      const closeBtn = toast.querySelector('.toast-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', function() {
          toast.style.opacity = '0';
          toast.style.transform = 'translateX(100%)';
          setTimeout(() => {
            toastContainer.removeChild(toast);
          }, 300);
        });
      }
    }
    
    // Filter functionality
    const filterBtn = document.querySelector('.filter-btn');
    const filterReset = document.querySelector('.filter-reset');
    
    if (filterBtn) {
      filterBtn.addEventListener('click', function() {
        createToast('Filtres appliqués', 'La liste des capteurs a été filtrée selon vos critères.', 'info');
      });
    }
    
    if (filterReset) {
      filterReset.addEventListener('click', function() {
        // Reset filter selects
        document.querySelectorAll('.filter-select').forEach(select => {
          select.selectedIndex = 0;
        });
        
        createToast('Filtres réinitialisés', 'Tous les filtres ont été réinitialisés.', 'info');
      });
    }
    
    // Pagination functionality
    const pageBtns = document.querySelectorAll('.page-btn');
    
    if (pageBtns.length) {
      pageBtns.forEach(btn => {
        if (!btn.classList.contains('prev') && !btn.classList.contains('next')) {
          btn.addEventListener('click', function() {
            // Remove active class from all page buttons
            pageBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            btn.classList.add('active');
            
            createToast('Changement de page', `Affichage de la page ${btn.textContent}.`, 'info');
          });
        }
      });
      
      // Previous and next page buttons
      const prevBtn = document.querySelector('.page-btn.prev');
      const nextBtn = document.querySelector('.page-btn.next');
      
      if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function() {
          const activeBtn = document.querySelector('.page-btn.active');
          if (activeBtn && activeBtn.previousElementSibling && !activeBtn.previousElementSibling.classList.contains('prev')) {
            activeBtn.previousElementSibling.click();
          }
        });
        
        nextBtn.addEventListener('click', function() {
          const activeBtn = document.querySelector('.page-btn.active');
          if (activeBtn && activeBtn.nextElementSibling && !activeBtn.nextElementSibling.classList.contains('next') && !activeBtn.nextElementSibling.classList.contains('page-ellipsis')) {
            activeBtn.nextElementSibling.click();
          }
        });
      }
    }
  });
  