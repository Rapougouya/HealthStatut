document.addEventListener('DOMContentLoaded', function() {
    // Initialize all animations
    const animateFadeIn = document.querySelectorAll('.section-card');
    animateFadeIn.forEach((element, index) => {
      element.classList.add('animate-fade-in');
      element.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Setup the charts
    setupCharts();
    
    // Add event listeners for chart tab switching
    const chartTabButtons = document.querySelectorAll('.chart-tab-btn');
    const chartContainers = document.querySelectorAll('.chart-container');
    
    chartTabButtons.forEach(button => {
      button.addEventListener('click', function() {
        // Remove active class from all buttons and containers
        chartTabButtons.forEach(btn => btn.classList.remove('active'));
        chartContainers.forEach(container => container.classList.remove('active'));
        
        // Add active class to current button and container
        this.classList.add('active');
        const targetChart = document.getElementById(`${this.dataset.chart}-chart`);
        targetChart.classList.add('active');
      });
    });
    
    // Add event listeners for period selection
    const periodButtons = document.querySelectorAll('.period-btn');
    periodButtons.forEach(button => {
      button.addEventListener('click', function() {
        periodButtons.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        // Update charts with new period
        updateChartsForPeriod(this.dataset.period);
      });
    });
    
    // Add event listener for refresh button
    const refreshBtn = document.querySelector('.refresh-btn');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', function() {
        this.classList.add('animate-pulse');
        refreshVitalSigns();
        
        // Remove animation class after animation completes
        setTimeout(() => {
          this.classList.remove('animate-pulse');
        }, 2000);
        
        // Show a toast notification
        showToast('Mise à jour', 'Signes vitaux mis à jour avec succès', 'success');
      });
    }
    
    // Add event listeners for sensor config buttons
    const configButtons = document.querySelectorAll('.action-btn.config');
    configButtons.forEach(button => {
      button.addEventListener('click', function() {
        const sensorId = this.dataset.sensorId;
        showToast('Configuration', `Configuration du capteur ${sensorId} en cours...`, 'info', {
          text: 'Ouvrir',
          callback: () => {
            window.location.href = `/capteurs/${sensorId}/edit`;
          }
        });
      });
    });
    
    // Add event listener for contact button
    const contactBtn = document.querySelector('.action-btn.contact');
    if (contactBtn) {
      contactBtn.addEventListener('click', function() {
        showToast('Contact', 'Fonctionnalité de contact en cours de développement', 'info');
      });
    }
    
    // Toggle sidebar
    const menuButton = document.querySelector('.menu-button');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (menuButton && sidebar && mainContent) {
      menuButton.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
      });
      
      // Check localStorage for sidebar state preference
      const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
      }
      
      // Save preference when toggling
      menuButton.addEventListener('click', function() {
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
      });
    }
  });
  
  /**
   * Setup and initialize all charts
   */
  function setupCharts() {
    // Heart Rate Chart
    setupHeartRateChart();
    
    // Temperature Chart
    setupTemperatureChart();
    
    // Oxygen Saturation Chart
    setupOxygenChart();
    
    // Blood Pressure Chart
    setupBloodPressureChart();
  }
  
  /**
   * Set up the heart rate chart
   */
  function setupHeartRateChart() {
    const ctx = document.getElementById('heart-rate-chart');
    if (!ctx) return;
    
    // Create a gradient
    const chartArea = ctx.getBoundingClientRect();
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, chartArea.height);
    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.3)');
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0.0)');
    
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: generateTimeLabels(24),
        datasets: [{
          label: 'Rythme cardiaque',
          data: generateRandomData(24, 60, 90),
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: gradient,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: 'white',
          pointBorderColor: 'rgba(54, 162, 235, 1)',
          tension: 0.4,
          fill: true,
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
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: '#ddd',
            borderWidth: 1,
            caretSize: 6,
            cornerRadius: 6,
            displayColors: false,
            padding: 10,
            callbacks: {
              label: function(context) {
                return `${context.parsed.y} bpm`;
              }
            }
          }
        },
        scales: {
          y: {
            suggestedMin: 50,
            suggestedMax: 100,
            grid: {
              drawBorder: false,
              color: 'rgba(200, 200, 200, 0.15)'
            },
            ticks: {
              callback: function(value) {
                return value + ' bpm';
              }
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  }
  
  /**
   * Set up the temperature chart
   */
  function setupTemperatureChart() {
    const ctx = document.getElementById('temperature-chart');
    if (!ctx) return;
    
    // Create a gradient
    const chartArea = ctx.getBoundingClientRect();
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, chartArea.height);
    gradient.addColorStop(0, 'rgba(255, 99, 132, 0.3)');
    gradient.addColorStop(1, 'rgba(255, 99, 132, 0.0)');
    
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: generateTimeLabels(24),
        datasets: [{
          label: 'Température',
          data: generateRandomData(24, 36.5, 38),
          borderColor: 'rgba(255, 99, 132, 1)',
          backgroundColor: gradient,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: 'white',
          pointBorderColor: 'rgba(255, 99, 132, 1)',
          tension: 0.4,
          fill: true,
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
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: '#ddd',
            borderWidth: 1,
            caretSize: 6,
            cornerRadius: 6,
            displayColors: false,
            padding: 10,
            callbacks: {
              label: function(context) {
                return `${context.parsed.y.toFixed(1)}°C`;
              }
            }
          }
        },
        scales: {
          y: {
            suggestedMin: 36,
            suggestedMax: 39,
            grid: {
              drawBorder: false,
              color: 'rgba(200, 200, 200, 0.15)'
            },
            ticks: {
              callback: function(value) {
                return value + '°C';
              }
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  }
  
  /**
   * Set up the oxygen saturation chart
   */
  function setupOxygenChart() {
    const ctx = document.getElementById('oxygen-chart');
    if (!ctx) return;
    
    // Create a gradient
    const chartArea = ctx.getBoundingClientRect();
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, chartArea.height);
    gradient.addColorStop(0, 'rgba(75, 192, 192, 0.3)');
    gradient.addColorStop(1, 'rgba(75, 192, 192, 0.0)');
    
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: generateTimeLabels(24),
        datasets: [{
          label: 'Saturation O₂',
          data: generateRandomData(24, 94, 99),
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: gradient,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: 'white',
          pointBorderColor: 'rgba(75, 192, 192, 1)',
          tension: 0.4,
          fill: true,
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
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: '#ddd',
            borderWidth: 1,
            caretSize: 6,
            cornerRadius: 6,
            displayColors: false,
            padding: 10,
            callbacks: {
              label: function(context) {
                return `${context.parsed.y}%`;
              }
            }
          }
        },
        scales: {
          y: {
            suggestedMin: 90,
            suggestedMax: 100,
            grid: {
              drawBorder: false,
              color: 'rgba(200, 200, 200, 0.15)'
            },
            ticks: {
              callback: function(value) {
                return value + '%';
              }
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  }
  
  /**
   * Set up the blood pressure chart
   */
  function setupBloodPressureChart() {
    const ctx = document.getElementById('blood-pressure-chart');
    if (!ctx) return;
    
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: generateTimeLabels(24),
        datasets: [
          {
            label: 'Systolique',
            data: generateRandomData(24, 120, 140),
            borderColor: 'rgba(255, 159, 64, 1)',
            backgroundColor: 'rgba(255, 159, 64, 0.1)',
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: 'white',
            pointBorderColor: 'rgba(255, 159, 64, 1)',
            tension: 0.4,
            fill: false,
          },
          {
            label: 'Diastolique',
            data: generateRandomData(24, 70, 90),
            borderColor: 'rgba(153, 102, 255, 1)',
            backgroundColor: 'rgba(153, 102, 255, 0.1)',
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: 'white',
            pointBorderColor: 'rgba(153, 102, 255, 1)',
            tension: 0.4,
            fill: false,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              boxWidth: 10,
              usePointStyle: true,
              pointStyle: 'circle',
              font: {
                size: 12
              }
            }
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: '#ddd',
            borderWidth: 1,
            caretSize: 6,
            cornerRadius: 6,
            displayColors: true,
            padding: 10,
            callbacks: {
              label: function(context) {
                return `${context.dataset.label}: ${context.parsed.y} mmHg`;
              }
            }
          }
        },
        scales: {
          y: {
            suggestedMin: 60,
            suggestedMax: 150,
            grid: {
              drawBorder: false,
              color: 'rgba(200, 200, 200, 0.15)'
            },
            ticks: {
              callback: function(value) {
                return value + ' mmHg';
              }
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  }
  
  /**
   * Generate time labels for charts
   * @param {number} count - Number of labels to generate
   * @returns {Array} - Array of time labels
   */
  function generateTimeLabels(count) {
    const labels = [];
    const now = new Date();
    
    for (let i = count - 1; i >= 0; i--) {
      const time = new Date(now.getTime() - (i * 60 * 60 * 1000));
      labels.push(time.getHours() + ':00');
    }
    
    return labels;
  }
  
  /**
   * Generate random data for charts
   * @param {number} count - Number of data points to generate
   * @param {number} min - Minimum value
   * @param {number} max - Maximum value
   * @returns {Array} - Array of random data
   */
  function generateRandomData(count, min, max) {
    const data = [];
    for (let i = 0; i < count; i++) {
      data.push(min + Math.random() * (max - min));
    }
    return data;
  }
  
  /**
   * Update charts for a different time period
   * @param {string} period - Period to update charts for (day, week, month)
   */
  function updateChartsForPeriod(period) {
    let count = 24; // Default for day
    
    if (period === 'week') {
      count = 7;
    } else if (period === 'month') {
      count = 30;
    }
    
    // Update each chart with new data
    const charts = document.querySelectorAll('.chart-container');
    charts.forEach(container => {
      const chartId = container.id;
      const chartInstance = Chart.getChart(container);
      
      if (chartInstance) {
        // Update labels
        let labels = [];
        
        if (period === 'day') {
          labels = generateTimeLabels(count);
        } else if (period === 'week') {
          const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
          const now = new Date();
          for (let i = count - 1; i >= 0; i--) {
            const date = new Date(now.getTime() - (i * 24 * 60 * 60 * 1000));
            labels.push(days[date.getDay()]);
          }
        } else if (period === 'month') {
          for (let i = 1; i <= count; i++) {
            labels.push(i);
          }
        }
        
        chartInstance.data.labels = labels;
        
        // Update data
        chartInstance.data.datasets.forEach(dataset => {
          let min, max;
          
          // Determine min and max based on chart type
          if (chartId === 'heart-rate-chart') {
            min = 60;
            max = 90;
          } else if (chartId === 'temperature-chart') {
            min = 36.5;
            max = 38;
          } else if (chartId === 'oxygen-chart') {
            min = 94;
            max = 99;
          } else {
            // Blood pressure
            if (dataset.label === 'Systolique') {
              min = 120;
              max = 140;
            } else {
              min = 70;
              max = 90;
            }
          }
          
          dataset.data = generateRandomData(count, min, max);
        });
        
        chartInstance.update();
      }
    });
    
    showToast('Graphique', `Données affichées pour la période: ${period === 'day' ? 'Jour' : period === 'week' ? 'Semaine' : 'Mois'}`, 'info');
  }
  
  /**
   * Refresh vital signs data
   */
  function refreshVitalSigns() {
    // Simulate API call to refresh data
    setTimeout(() => {
      const vitalCards = document.querySelectorAll('.vital-card');
      
      vitalCards.forEach(card => {
        const valueElement = card.querySelector('.vital-value');
        if (!valueElement) return;
        
        const currentText = valueElement.textContent;
        const numberMatch = currentText.match(/[\d.]+/);
        if (!numberMatch) return;
        
        const currentValue = parseFloat(numberMatch[0]);
        const unitMatch = currentText.match(/[^\d.]+$/);
        const unit = unitMatch ? unitMatch[0].trim() : '';
        
        // Generate a slightly different value
        let newValue = currentValue + (Math.random() * 0.4 - 0.2);
        newValue = parseFloat(newValue.toFixed(1));
        
        // Update the value
        valueElement.innerHTML = `${newValue} <span class="vital-unit">${unit}</span>`;
        
        // Update trend indicators
        const trendElement = card.querySelector('.vital-trend');
        if (trendElement) {
          const isUp = Math.random() > 0.5;
          const trendValue = Math.floor(Math.random() * 5) + 1;
          
          trendElement.className = `vital-trend ${isUp ? 'up' : 'down'}`;
          trendElement.innerHTML = `<i class="icon-${isUp ? 'trend-up' : 'trend-down'}"></i> ${trendValue}%`;
        }
        
        // Add a temporary highlight effect
        card.classList.add('animate-pulse');
        setTimeout(() => {
          card.classList.remove('animate-pulse');
        }, 2000);
      });
    }, 500);
  }
  
  /**
   * Show a toast notification
   * @param {string} title - Toast title
   * @param {string} message - Toast message
   * @param {string} type - Toast type (info, success, warning, error)
   * @param {object} action - Optional action button (text, callback)
   */
  function showToast(title, message, type = 'info', action = null) {
    const toastContainer = document.querySelector('.toast-container');
    
    // Create toast container if it doesn't exist
    if (!toastContainer) {
      const container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
      
      return setTimeout(() => showToast(title, message, type, action), 100);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    // Create toast content
    let toastHTML = `
      <div class="toast-icon">
        <i class="icon-${type === 'success' ? 'check' : type === 'warning' ? 'alert' : type === 'error' ? 'alert' : 'info'}" 
           style="color: var(--${type === 'success' ? 'success' : type === 'warning' ? 'warning' : type === 'error' ? 'alert' : 'info'}-color);"></i>
      </div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
    `;
    
    // Add action button if provided
    if (action && action.text) {
      toastHTML += `<button class="toast-action">${action.text}</button>`;
    }
    
    toast.innerHTML = toastHTML;
    
    // Add action button event listener
    if (action && action.text) {
      const actionBtn = toast.querySelector('.toast-action');
      if (actionBtn && action.callback) {
        actionBtn.addEventListener('click', action.callback);
      }
    }
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Remove toast after delay
    setTimeout(() => {
      toast.classList.add('hide');
      setTimeout(() => {
        toast.remove();
      }, 300); // Match the animation duration
    }, 5000);
  }