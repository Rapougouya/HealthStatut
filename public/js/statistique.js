document.addEventListener('DOMContentLoaded', function() {
  // Initialiser les gestionnaires d'événements
  initFilterEvents();
  
  // Initialiser le tableau de données
  initDataTable();
  
  // Initialiser les graphiques
  initCharts();
  
  // Initialiser la pagination
  initPagination();
});

function initFilterEvents() {
  const applyFilter = document.getElementById('apply-filter');
  if (applyFilter) {
      applyFilter.addEventListener('click', function() {
          // Récupérer les valeurs des filtres
          const patientId = document.getElementById('patient-select').value;
          const startDate = document.getElementById('start-date').value;
          const endDate = document.getElementById('end-date').value;
          
          console.log(`Filtrage des données pour le patient ${patientId} du ${startDate} au ${endDate}`);
          
          // Recharger les données avec les filtres
          loadData(patientId, startDate, endDate);
      });
  }
  
  // Gestionnaire pour les boutons de vue (liste/grille)
  const viewButtons = document.querySelectorAll('.view-btn');
  viewButtons.forEach(button => {
      button.addEventListener('click', function() {
          // Supprimer la classe active de tous les boutons
          viewButtons.forEach(btn => btn.classList.remove('active'));
          
          // Ajouter la classe active au bouton cliqué
          this.classList.add('active');
          
          // Changer la vue selon le bouton
          const viewType = this.getAttribute('data-view');
          toggleView(viewType);
      });
  });
}

function toggleView(viewType) {
  const chartsContainer = document.querySelector('.charts-container');
  const dataTableSection = document.querySelector('.data-table-section');
  
  if (viewType === 'grid') {
      chartsContainer.style.display = 'grid';
      dataTableSection.style.display = 'none';
  } else if (viewType === 'list') {
      chartsContainer.style.display = 'none';
      dataTableSection.style.display = 'block';
  }
}

function loadData(patientId, startDate, endDate) {
  // Simulation de chargement des données (à remplacer par un appel API réel)
  console.log(`Chargement des données pour: Patient ${patientId}, du ${startDate} au ${endDate}`);
  
  // Pour la démo, générer des données aléatoires
  const days = 30;
  const heartRateData = generateRandomData(days, 60, 100);
  const bloodPressureData = generateRandomData(days, 110, 150);
  const oxygenData = generateRandomData(days, 92, 99);
  const tempData = generateRandomData(days, 36.5, 37.8);
  
  // Mettre à jour les graphiques
  updateCharts(heartRateData, bloodPressureData, oxygenData, tempData);
  
  // Mettre à jour le tableau de données
  updateDataTable(heartRateData, bloodPressureData, oxygenData, tempData);
}

function generateRandomData(days, min, max) {
  const data = [];
  const today = new Date();
  
  for (let i = 0; i < days; i++) {
      const date = new Date();
      date.setDate(today.getDate() - (days - i));
      
      data.push({
          date: date.toISOString().split('T')[0],
          value: parseFloat((Math.random() * (max - min) + min).toFixed(1))
      });
  }
  
  return data;
}

function initCharts() {
  // Simulation de données initiales
  const days = 30;
  const heartRateData = generateRandomData(days, 60, 100);
  const bloodPressureData = generateRandomData(days, 110, 150);
  const oxygenData = generateRandomData(days, 92, 99);
  const tempData = generateRandomData(days, 36.5, 37.8);
  
  // Créer les graphiques
  createHeartRateChart('heart-rate-chart', heartRateData);
  createBloodPressureChart('blood-pressure-chart', bloodPressureData);
  createOxygenChart('oxygen-chart', oxygenData);
  createTemperatureChart('temp-chart', tempData);
  
  // Ajouter les statistiques
  updateStats(heartRateData, bloodPressureData, oxygenData, tempData);
}

function createHeartRateChart(elementId, data) {
  const canvas = document.getElementById(elementId);
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  
  // Simulation de création d'un graphique (à remplacer par une vraie bibliothèque de graphiques)
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
  ctx.strokeStyle = '#dc3545';
  
  const xScale = canvas.width / (data.length - 1);
  const yScale = canvas.height / 100;
  
  data.forEach((item, index) => {
      const x = index * xScale;
      const y = canvas.height - item.value * yScale;
      
      if (index === 0) {
          ctx.moveTo(x, y);
      } else {
          ctx.lineTo(x, y);
      }
  });
  
  ctx.stroke();
}

function createBloodPressureChart(elementId, data) {
  const canvas = document.getElementById(elementId);
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  
  // Simulation
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
  ctx.strokeStyle = '#007bff';
  
  const xScale = canvas.width / (data.length - 1);
  const yScale = canvas.height / 200;
  
  data.forEach((item, index) => {
      const x = index * xScale;
      const y = canvas.height - item.value * yScale;
      
      if (index === 0) {
          ctx.moveTo(x, y);
      } else {
          ctx.lineTo(x, y);
      }
  });
  
  ctx.stroke();
}

function createOxygenChart(elementId, data) {
  const canvas = document.getElementById(elementId);
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  
  // Simulation
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
  ctx.strokeStyle = '#28a745';
  
  const xScale = canvas.width / (data.length - 1);
  const yScale = canvas.height / 30;
  
  data.forEach((item, index) => {
      const x = index * xScale;
      const y = canvas.height - (item.value - 70) * yScale;
      
      if (index === 0) {
          ctx.moveTo(x, y);
      } else {
          ctx.lineTo(x, y);
      }
  });
  
  ctx.stroke();
}

function createTemperatureChart(elementId, data) {
  const canvas = document.getElementById(elementId);
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  
  // Simulation
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
  ctx.strokeStyle = '#fd7e14';
  
  const xScale = canvas.width / (data.length - 1);
  const yScale = canvas.height / 5;
  
  data.forEach((item, index) => {
      const x = index * xScale;
      const y = canvas.height - (item.value - 35) * yScale;
      
      if (index === 0) {
          ctx.moveTo(x, y);
      } else {
          ctx.lineTo(x, y);
      }
  });
  
  ctx.stroke();
}

function updateCharts(heartRateData, bloodPressureData, oxygenData, tempData) {
  createHeartRateChart('heart-rate-chart', heartRateData);
  createBloodPressureChart('blood-pressure-chart', bloodPressureData);
  createOxygenChart('oxygen-chart', oxygenData);
  createTemperatureChart('temp-chart', tempData);
  
  updateStats(heartRateData, bloodPressureData, oxygenData, tempData);
}

function updateStats(heartRateData, bloodPressureData, oxygenData, tempData) {
  // Calculer les statistiques
  updateStatSection('heart-rate-stats', heartRateData, 'bpm');
  updateStatSection('blood-pressure-stats', bloodPressureData, 'mmHg');
  updateStatSection('oxygen-stats', oxygenData, '%');
  updateStatSection('temp-stats', tempData, '°C');
}

function updateStatSection(elementId, data, unit) {
  const container = document.getElementById(elementId);
  if (!container) return;
  
  // Calculer les statistiques
  const values = data.map(item => item.value);
  const min = Math.min(...values).toFixed(1);
  const max = Math.max(...values).toFixed(1);
  const avg = (values.reduce((a, b) => a + b, 0) / values.length).toFixed(1);
  const last = values[values.length - 1].toFixed(1);
  
  // Mettre à jour le HTML
  container.innerHTML = `
      <div class="stat">
          <div class="stat-value">${min}</div>
          <div class="stat-label">Min ${unit}</div>
      </div>
      <div class="stat">
          <div class="stat-value">${max}</div>
          <div class="stat-label">Max ${unit}</div>
      </div>
      <div class="stat">
          <div class="stat-value">${avg}</div>
          <div class="stat-label">Moy ${unit}</div>
      </div>
      <div class="stat">
          <div class="stat-value">${last}</div>
          <div class="stat-label">Actuel ${unit}</div>
      </div>
  `;
}

function initDataTable() {
  // Initialiser le tableau avec des données aléatoires
  const days = 30;
  const heartRateData = generateRandomData(days, 60, 100);
  const bloodPressureData = generateRandomData(days, 110, 150);
  const oxygenData = generateRandomData(days, 92, 99);
  const tempData = generateRandomData(days, 36.5, 37.8);
  
  updateDataTable(heartRateData, bloodPressureData, oxygenData, tempData);
}

function updateDataTable(heartRateData, bloodPressureData, oxygenData, tempData) {
  const tableBody = document.querySelector('.data-table tbody');
  if (!tableBody) return;
  
  tableBody.innerHTML = '';
  
  // Générer les lignes du tableau
  for (let i = 0; i < heartRateData.length; i++) {
      const row = document.createElement('tr');
      
      row.innerHTML = `
          <td>${heartRateData[i].date}</td>
          <td>${heartRateData[i].value} bpm</td>
          <td>${bloodPressureData[i].value} mmHg</td>
          <td>${oxygenData[i].value}%</td>
          <td>${tempData[i].value}°C</td>
          <td>
              <button class="action-btn" title="Voir détails">
                  <i class="fas fa-eye"></i>
              </button>
              <button class="action-btn" title="Exporter">
                  <i class="fas fa-download"></i>
              </button>
          </td>
      `;
      
      tableBody.appendChild(row);
  }
}

function initPagination() {
  const prevButton = document.querySelector('.pagination-btn[data-action="prev"]');
  const nextButton = document.querySelector('.pagination-btn[data-action="next"]');
  let currentPage = 1;
  const totalPages = 5; // Pour la démo
  
  // Mettre à jour le texte de pagination
  document.querySelector('.page-info').textContent = `Page ${currentPage} sur ${totalPages}`;
  
  if (prevButton) {
      prevButton.addEventListener('click', () => {
          if (currentPage > 1) {
              currentPage--;
              document.querySelector('.page-info').textContent = `Page ${currentPage} sur ${totalPages}`;
              // Simulation de changement de page
              loadData('P-123', '2023-01-01', '2023-01-31');
          }
      });
  }
  
  if (nextButton) {
      nextButton.addEventListener('click', () => {
          if (currentPage < totalPages) {
              currentPage++;
              document.querySelector('.page-info').textContent = `Page ${currentPage} sur ${totalPages}`;
              // Simulation de changement de page
              loadData('P-123', '2023-01-01', '2023-01-31');
          }
      });
  }
}

// Fonction pour exporter les données
function exportData(chartId) {
  console.log(`Exportation des données du graphique: ${chartId}`);
  
  // Simuler un téléchargement (à remplacer par un vrai mécanisme d'export)
  alert(`Les données du graphique ${chartId} ont été exportées.`);
}

// Initialiser les boutons d'exportation
document.querySelectorAll('.export-btn').forEach(button => {
  button.addEventListener('click', function() {
      const chartId = this.closest('.chart-card').querySelector('.chart-area').id;
      exportData(chartId);
  });
});
