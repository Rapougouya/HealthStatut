document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  
    // Notifications handling
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
  
    if (notificationBell && notificationDropdown) {
      notificationBell.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');
      });
  
      document.addEventListener('click', function(e) {
        if (!notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
          notificationDropdown.classList.remove('show');
        }
      });
    }
  
    // Toast notifications
    window.showToast = function(message, type = 'info') {
      const toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      toast.innerHTML = `
        <div class="toast-content">
          <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
          <span>${message}</span>
        </div>
      `;
  
      const container = document.getElementById('toastContainer');
      container.appendChild(toast);
  
      setTimeout(() => {
        toast.classList.add('show');
        setTimeout(() => {
          toast.classList.remove('show');
          setTimeout(() => toast.remove(), 300);
        }, 3000);
      }, 100);
    };
  
    // Fix the table layout for patient tables
  const patientsTables = document.querySelectorAll('.patients-table');
  patientsTables.forEach(table => {
    // Ensure action buttons are properly sized and aligned
    const actionCells = table.querySelectorAll('td:last-child');
    actionCells.forEach(cell => {
      cell.style.textAlign = 'center';
    });
  });

    // Mobile responsiveness
    const toggleSidebar = document.querySelector('.menu-button');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggleSidebar && sidebar) {
      toggleSidebar.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        document.querySelector('.main-content').classList.toggle('expanded');
      });
    }
    

    // Add keyboard navigation support for table rows
    const actionButtons = row.querySelectorAll('.action-btn');
    if (actionButtons.length) {
      row.setAttribute('tabindex', '0');
      
      row.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          actionButtons[0].click();
        }
      });
    }
    
    // Add smooth animations to stats cards
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.1}s`;
      card.classList.add('fade-in');
    });
  });