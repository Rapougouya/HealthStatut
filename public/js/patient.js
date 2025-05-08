// patient.js
document.addEventListener('DOMContentLoaded', function() {
  // Initialisation des graphiques
  initCharts();
  
  // Gestion des onglets des graphiques
  setupChartTabs();
  
  // Gestion des périodes
  setupPeriodSelector();
  
  // Bouton de rafraîchissement
  setupRefreshButton();
  
  // Gestion des capteurs
  setupSensorActions();
});

function initCharts() {
  const chartContainers = document.querySelectorAll('.chart-container');
  
  chartContainers.forEach(container => {
      if (container.classList.contains('active')) {
          loadChart(container);
      }
  });
}

function loadChart(container) {
  const chartType = container.dataset.type;
  const patientId = container.dataset.patientId;
  const ctx = document.createElement('canvas');
  container.innerHTML = '';
  container.appendChild(ctx);
  
  // Simuler des données (remplacer par un appel API réel)
  const labels = Array.from({length: 24}, (_, i) => `${i}h`);
  const data = Array.from({length: 24}, () => Math.floor(Math.random() * 100) + 50);
  
  new Chart(ctx, {
      type: 'line',
      data: {
          labels: labels,
          datasets: [{
              label: chartType,
              data: data,
              borderColor: '#4a6fa5',
              backgroundColor: 'rgba(74, 111, 165, 0.1)',
              borderWidth: 2,
              tension: 0.4,
              fill: true
          }]
      },
      options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
              legend: {
                  display: false
              },
              tooltip: {
                  mode: 'index',
                  intersect: false
              }
          },
          scales: {
              y: {
                  beginAtZero: false
              }
          }
      }
  });
}

function setupChartTabs() {
  const tabButtons = document.querySelectorAll('.chart-tab-btn');
  
  tabButtons.forEach(button => {
      button.addEventListener('click', () => {
          // Désactiver tous les onglets
          document.querySelectorAll('.chart-tab-btn').forEach(btn => {
              btn.classList.remove('active');
          });
          
          document.querySelectorAll('.chart-container').forEach(container => {
              container.classList.remove('active');
          });
          
          // Activer l'onglet sélectionné
          button.classList.add('active');
          const chartId = button.dataset.chart;
          const chartContainer = document.getElementById(`${chartId}-chart`);
          chartContainer.classList.add('active');
          
          // Charger le graphique s'il n'est pas déjà chargé
          if (chartContainer.children.length === 0) {
              loadChart(chartContainer);
          }
      });
  });
}

function setupPeriodSelector() {
  const periodButtons = document.querySelectorAll('.period-btn');
  
  periodButtons.forEach(button => {
      button.addEventListener('click', () => {
          periodButtons.forEach(btn => btn.classList.remove('active'));
          button.classList.add('active');
          
          // Ici, vous devriez recharger les données du graphique
          // en fonction de la période sélectionnée
          const activeChart = document.querySelector('.chart-container.active');
          if (activeChart) {
              activeChart.innerHTML = '';
              loadChart(activeChart);
          }
      });
  });
}

function setupRefreshButton() {
  const refreshBtn = document.querySelector('.refresh-btn');
  
  if (refreshBtn) {
      refreshBtn.addEventListener('click', () => {
          // Simuler un rechargement
          refreshBtn.classList.add('rotating');
          
          setTimeout(() => {
              refreshBtn.classList.remove('rotating');
              
              // Recharger les données
              const activeChart = document.querySelector('.chart-container.active');
              if (activeChart) {
                  activeChart.innerHTML = '';
                  loadChart(activeChart);
              }
              
              // Afficher une notification
              showToast('Données actualisées', 'success');
          }, 1000);
      });
  }
}

function setupSensorActions() {
  const configButtons = document.querySelectorAll('.action-btn.config');
  
  configButtons.forEach(button => {
      button.addEventListener('click', () => {
          const sensorId = button.dataset.sensorId;
          // Ici, vous devriez ouvrir un modal de configuration
          showToast(`Configuration du capteur ${sensorId}`, 'info');
      });
  });
}

function showToast(message, type) {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
      toast.classList.add('show');
  }, 10);
  
  setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
          toast.remove();
      }, 300);
  }, 3000);
}

// Animation pour le bouton de rafraîchissement
const style = document.createElement('style');
style.textContent = `
  .rotating {
      animation: rotate 1s linear infinite;
  }
  
  @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
  }
  
  .toast {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      padding: 12px 24px;
      border-radius: 4px;
      color: white;
      opacity: 0;
      transition: opacity 0.3s;
      z-index: 1000;
  }
  
  .toast.show {
      opacity: 1;
  }
  
  .toast-success {
      background-color: #28a745;
  }
  
  .toast-info {
      background-color: #17a2b8;
  }
  
  .toast-warning {
      background-color: #ffc107;
      color: #212529;
  }
  
  .toast-danger {
      background-color: #dc3545;
  }
`;
document.head.appendChild(style);