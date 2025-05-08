document.addEventListener('DOMContentLoaded', function() {
  // Toggle sidebar menu
  const menuButton = document.querySelector('.menu-button');
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('.main-content');
  
  if (menuButton && sidebar && mainContent) {
    menuButton.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    });
  }
  
  // Notification dropdown
  const notificationBell = document.getElementById('notificationBell');
  const notificationDropdown = document.getElementById('notificationDropdown');
  
  if (notificationBell && notificationDropdown) {
    notificationBell.addEventListener('click', function(event) {
      event.stopPropagation();
      notificationDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      if (notificationDropdown.classList.contains('show') && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.remove('show');
      }
    });
  }
  
  // Alert patient view action
  const viewAlertButtons = document.querySelectorAll('.action-btn[data-patient]');
  
  viewAlertButtons.forEach(button => {
    button.addEventListener('click', function() {
      const patientId = this.getAttribute('data-patient');
      // Redirect to patient detail page
      window.location.href = `patient.html?id=${patientId}`;
    });
  });
  
  // Quick action buttons
  const quickActionButtons = document.querySelectorAll('.quick-action-btn');
  
  quickActionButtons.forEach(button => {
    button.addEventListener('click', function() {
      const actionText = this.querySelector('span').textContent;
      showToast(`Action: ${actionText}`, 'Cette fonctionnalité sera bientôt disponible.');
    });
  });
  
  // Function to show toast notification
  window.showToast = function(title, message) {
    const toastContainer = document.getElementById('toastContainer');
    
    const toast = document.createElement('div');
    toast.className = 'toast';
    
    const toastIcon = document.createElement('div');
    toastIcon.className = 'toast-icon';
    toastIcon.innerHTML = '<i class="icon-info" style="color: var(--info-color);"></i>';
    
    const toastContent = document.createElement('div');
    toastContent.className = 'toast-content';
    
    const toastTitle = document.createElement('div');
    toastTitle.className = 'toast-title';
    toastTitle.textContent = title;
    
    const toastMessage = document.createElement('div');
    toastMessage.className = 'toast-message';
    toastMessage.textContent = message;
    
    const closeButton = document.createElement('button');
    closeButton.className = 'toast-close';
    closeButton.innerHTML = '&times;';
    closeButton.addEventListener('click', function() {
      toast.remove();
    });
    
    toastContent.appendChild(toastTitle);
    toastContent.appendChild(toastMessage);
    
    toast.appendChild(toastIcon);
    toast.appendChild(toastContent);
    toast.appendChild(closeButton);
    
    toastContainer.appendChild(toast);
    
    // Auto-remove toast after 5 seconds
    setTimeout(() => {
      toast.remove();
    }, 5000);
  };
});
