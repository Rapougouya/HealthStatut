document.addEventListener('DOMContentLoaded', function() {
  // Initialisation des onglets
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  
  tabButtons.forEach(button => {
      button.addEventListener('click', () => {
          const tabName = button.getAttribute('data-tab');
          
          // Désactiver tous les onglets
          tabButtons.forEach(btn => btn.classList.remove('active'));
          tabContents.forEach(content => content.classList.remove('active'));
          
          // Activer l'onglet sélectionné
          button.classList.add('active');
          document.getElementById(`${tabName}-tab`).classList.add('active');
          
          // Si c'est l'onglet des signes vitaux, redimensionner le graphique
          if (tabName === 'vitals' && window.vitalsChart) {
              setTimeout(() => {
                  window.vitalsChart.resize();
              }, 100);
          }
      });
  });
  
  // Gestion de l'affichage des dates personnalisées
  const dateRangeSelect = document.getElementById('date-range');
  const dateCustomRow = document.getElementById('date-custom-row');
  
  if (dateRangeSelect && dateCustomRow) {
      dateRangeSelect.addEventListener('change', function() {
          if (this.value === 'custom') {
              dateCustomRow.style.display = 'flex';
          } else {
              dateCustomRow.style.display = 'none';
          }
      });
  }
  
  // Initialisation du graphique des signes vitaux
  @if($selectedPatient && count($signesVitauxGraphique ?? []) > 0)
  const vitalsChartElement = document.getElementById('vitalsChart');
  if (vitalsChartElement) {
      const vitalsData = @json($signesVitauxGraphique);
      const dates = vitalsData.map(item => item.date);
      
      // Création d'un gradient pour les graphiques
      const createGradient = (ctx, color) => {
          const gradient = ctx.createLinearGradient(0, 0, 0, 400);
          gradient.addColorStop(0, `${color}80`);
          gradient.addColorStop(1, `${color}10`);
          return gradient;
      };
      
      const ctx = vitalsChartElement.getContext('2d');
      
      window.vitalsChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: dates,
              datasets: [
                  {
                      label: 'Fréquence cardiaque (bpm)',
                      data: vitalsData.map(item => item.rythme_cardiaque),
                      borderColor: '#ff6b6b',
                      backgroundColor: createGradient(ctx, '#ff6b6b'),
                      borderWidth: 2,
                      tension: 0.3,
                      fill: true,
                      pointBackgroundColor: '#fff',
                      pointBorderColor: '#ff6b6b',
                      pointBorderWidth: 2,
                      pointRadius: 4,
                      pointHoverRadius: 6
                  },
                  {
                      label: 'Température (°C)',
                      data: vitalsData.map(item => item.temperature),
                      borderColor: '#ff922b',
                      backgroundColor: createGradient(ctx, '#ff922b'),
                      borderWidth: 2,
                      tension: 0.3,
                      fill: true,
                      pointBackgroundColor: '#fff',
                      pointBorderColor: '#ff922b',
                      pointBorderWidth: 2,
                      pointRadius: 4,
                      pointHoverRadius: 6
                  },
                  {
                      label: 'Saturation O2 (%)',
                      data: vitalsData.map(item => item.saturation_oxygene),
                      borderColor: '#20c997',
                      backgroundColor: createGradient(ctx, '#20c997'),
                      borderWidth: 2,
                      tension: 0.3,
                      fill: true,
                      pointBackgroundColor: '#fff',
                      pointBorderColor: '#20c997',
                      pointBorderWidth: 2,
                      pointRadius: 4,
                      pointHoverRadius: 6
                  }
              ]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  tooltip: {
                      mode: 'index',
                      intersect: false,
                      backgroundColor: 'rgba(0, 0, 0, 0.8)',
                      titleFont: { size: 14, weight: 'bold' },
                      bodyFont: { size: 12 },
                      padding: 12,
                      cornerRadius: 8,
                      displayColors: true,
                      callbacks: {
                          label: function(context) {
                              let label = context.dataset.label || '';
                              if (label) {
                                  label += ': ';
                              }
                              if (context.parsed.y !== null) {
                                  label += context.parsed.y;
                              }
                              return label;
                          }
                      }
                  },
                  legend: {
                      position: 'top',
                      labels: {
                          usePointStyle: true,
                          padding: 20,
                          font: {
                              size: 12
                          }
                      }
                  }
              },
              scales: {
                  x: {
                      grid: {
                          display: false
                      },
                      ticks: {
                          color: '#6c757d'
                      }
                  },
                  y: {
                      beginAtZero: false,
                      grid: {
                          color: 'rgba(0, 0, 0, 0.05)'
                      },
                      ticks: {
                          color: '#6c757d'
                      }
                  }
              },
              interaction: {
                  mode: 'nearest',
                  axis: 'x',
                  intersect: false
              }
          }
      });
  }
  @endif
  
  // Gestion des modals
  const qrScanModal = document.getElementById('qr-scan-modal');
  const qrGenerateModal = document.getElementById('qr-generate-modal');
  const scanQrButton = document.getElementById('scan-qr');
  const generateQrButton = document.getElementById('generate-qr');
  const closeButtons = document.querySelectorAll('.close-modal');
  
  // Fonction pour fermer tous les modals
  const closeAllModals = () => {
      qrScanModal.classList.remove('active');
      qrGenerateModal.classList.remove('active');
      
      // Arrêter le scanner QR si actif
      if (window.qrScanner) {
          window.qrScanner.stop().then(() => {
              const qrVideo = document.getElementById('qr-video');
              if (qrVideo) {
                  qrVideo.style.display = 'none';
              }
              const scannerPlaceholder = document.getElementById('scanner-placeholder');
              if (scannerPlaceholder) {
                  scannerPlaceholder.style.display = 'flex';
              }
          }).catch(err => {
              console.error('Erreur lors de l\'arrêt du scanner:', err);
          });
      }
  };
  
  // Ouverture du scanner QR
  if (scanQrButton) {
      scanQrButton.addEventListener('click', function() {
          qrScanModal.classList.add('active');
          initQrScanner();
      });
  }
  
  // Génération du QR code
  if (generateQrButton) {
      generateQrButton.addEventListener('click', function() {
          qrGenerateModal.classList.add('active');
          generateQrCode();
      });
  }
  
  // Fermeture des modals
  closeButtons.forEach(button => {
      button.addEventListener('click', closeAllModals);
  });
  
  // Fermer en cliquant en dehors du modal
  document.addEventListener('click', function(e) {
      if (e.target.classList.contains('modal-overlay')) {
          closeAllModals();
      }
  });
  
  // Fermer avec la touche ESC
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          closeAllModals();
      }
  });
  
  // Autocomplete pour la recherche de patients
  const patientSearch = document.getElementById('patient-search');
  const searchResults = document.getElementById('patient-search-results');
  
  if (patientSearch) {
      patientSearch.addEventListener('input', debounce(function() {
          const query = this.value.trim();
          if (query.length < 2) {
              searchResults.innerHTML = '';
              searchResults.style.display = 'none';
              return;
          }
          
          fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(query)}`, {
              headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
          })
          .then(response => response.json())
          .then(data => {
              searchResults.innerHTML = '';
              
              if (data.length === 0) {
                  searchResults.innerHTML = '<div class="no-results"><i class="fas fa-search"></i> Aucun patient trouvé</div>';
                  searchResults.style.display = 'block';
                  return;
              }
              
              data.forEach(patient => {
                  const item = document.createElement('div');
                  item.className = 'search-result-item';
                  item.innerHTML = `
                      <div class="patient-avatar">
                          <img src="${patient.photo_url || '{{ asset('images/default-avatar.png') }}'}" alt="${patient.nom_complet}">
                      </div>
                      <div class="patient-info">
                          <div class="patient-name">${patient.nom_complet}</div>
                          <div class="patient-meta"><i class="fas fa-id-card"></i> ID: ${patient.numero_dossier} | <i class="fas fa-birthday-cake"></i> ${patient.age} ans</div>
                      </div>
                  `;
                  item.addEventListener('click', function() {
                      window.location.href = `{{ route('rapports.index') }}?patient_id=${patient.id}`;
                  });
                  searchResults.appendChild(item);
              });
              
              searchResults.style.display = 'block';
          })
          .catch(error => {
              console.error('Erreur:', error);
              showToast('error', 'Erreur', 'Une erreur est survenue lors de la recherche');
          });
      }, 300));
      
      // Cacher les résultats lorsque l'on clique en dehors
      document.addEventListener('click', function(e) {
          if (!patientSearch.contains(e.target) && !searchResults.contains(e.target)) {
              searchResults.style.display = 'none';
          }
      });
  }
  
  // Filtrer les résultats de laboratoire
  const labTypeFilter = document.getElementById('lab-type-filter');
  if (labTypeFilter) {
      labTypeFilter.addEventListener('change', function() {
          const selectedType = this.value;
          const labCards = document.querySelectorAll('.lab-card');
          let visibleCount = 0;
          
          labCards.forEach(card => {
              if (selectedType === 'all' || card.dataset.type === selectedType) {
                  card.style.display = 'block';
                  visibleCount++;
              } else {
                  card.style.display = 'none';
              }
          });
          
          // Afficher un message si aucun résultat ne correspond au filtre
          const noResultsElement = document.querySelector('.no-lab-results');
          if (noResultsElement) {
              if (visibleCount === 0 && selectedType !== 'all') {
                  noResultsElement.textContent = 'Aucun résultat ne correspond au filtre sélectionné';
                  noResultsElement.style.display = 'block';
              } else {
                  noResultsElement.style.display = 'none';
              }
          }
      });
  }
  
  // Fonction utilitaire pour le debounce
  function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
          const later = () => {
              clearTimeout(timeout);
              func.apply(this, args);
          };
          clearTimeout(timeout);
          timeout = setTimeout(later, wait);
      };
  }
  
  // Fonction pour initialiser le scanner QR
  function initQrScanner() {
      const scannerPlaceholder = document.getElementById('scanner-placeholder');
      const qrVideo = document.getElementById('qr-video');
      
      scannerPlaceholder.style.display = 'none';
      qrVideo.style.display = 'block';
      
      // Vérifier si la caméra est disponible
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          scannerPlaceholder.style.display = 'flex';
          scannerPlaceholder.innerHTML = `
              <i class="fas fa-video-slash fa-4x"></i>
              <p>L'accès à la caméra n'est pas disponible</p>
          `;
          showToast('error', 'Erreur', 'Votre appareil ne supporte pas la caméra ou l\'accès a été refusé');
          return;
      }
      
      // Initialisation du scanner HTML5-QRCode
      window.qrScanner = new Html5Qrcode("qr-video");
      window.qrScanner.start(
          { facingMode: "environment" },
          {
              fps: 10,
              qrbox: 250
          },
          (qrCodeMessage) => {
              // Succès: rediriger vers la page du patient
              showToast('success', 'Succès', 'Code QR scanné avec succès');
              window.qrScanner.stop().then(() => {
                  window.location.href = qrCodeMessage;
              }).catch(err => {
                  console.error('Erreur lors de l\'arrêt du scanner:', err);
                  window.location.href = qrCodeMessage;
              });
          },
          (errorMessage) => {
              // Erreur: nous l'ignorons car c'est probablement juste qu'aucun QR n'a été détecté
              console.log(errorMessage);
          }
      ).catch((err) => {
          console.error('Erreur du scanner QR:', err);
          scannerPlaceholder.style.display = 'flex';
          scannerPlaceholder.innerHTML = `
              <i class="fas fa-exclamation-triangle fa-4x"></i>
              <p>Impossible d'accéder à la caméra</p>
          `;
          qrVideo.style.display = 'none';
          showToast('error', 'Erreur', 'Impossible d\'accéder à la caméra');
      });
  }
  
  // Fonction pour générer un QR code
  function generateQrCode() {
      @if($selectedPatient)
      const qrcodeDiv = document.getElementById('qrcode');
      qrcodeDiv.innerHTML = '';
      
      // Créer un QR code avec une marge et un niveau de correction d'erreur élevé
      const qr = qrcode(0, 'H');
      qr.addData('{{ route('patients.show', $selectedPatient->id) }}');
      qr.make();
      
      // Créer une image SVG pour une meilleure qualité
      const svg = qr.createSvgTag({
          scalable: true,
          margin: 4,
          color: '#4a6fa5',
          alt: `QR Code pour {{ $selectedPatient->nom_complet }}`
      });
      
      qrcodeDiv.innerHTML = svg;
      
      // Mise à jour des informations du patient
      document.getElementById('qr-patient-name').textContent = '{{ $selectedPatient->nom_complet }}';
      document.getElementById('qr-patient-id').textContent = 'ID: {{ $selectedPatient->numero_dossier }}';
      
      // Configurer les boutons de téléchargement et d'impression
      const downloadBtn = document.getElementById('download-qr');
      const printBtn = document.getElementById('print-qr');
      
      downloadBtn.addEventListener('click', function() {
          // Convertir le SVG en PNG pour le téléchargement
          const svgElement = qrcodeDiv.querySelector('svg');
          const serializer = new XMLSerializer();
          const svgStr = serializer.serializeToString(svgElement);
          
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          const img = new Image();
          
          img.onload = function() {
              canvas.width = img.width;
              canvas.height = img.height;
              ctx.drawImage(img, 0, 0);
              
              const link = document.createElement('a');
              link.download = `qrcode-patient-{{ $selectedPatient->numero_dossier }}.png`;
              link.href = canvas.toDataURL('image/png');
              link.click();
          };
          
          img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgStr)));
      });
      
      printBtn.addEventListener('click', function() {
          const printWindow = window.open('', '_blank');
          printWindow.document.write(`
              <!DOCTYPE html>
              <html>
                  <head>
                      <title>QR Code - {{ $selectedPatient->nom_complet }}</title>
                      <style>
                          body { 
                              font-family: Arial, sans-serif; 
                              text-align: center; 
                              padding: 50px; 
                              display: flex;
                              flex-direction: column;
                              align-items: center;
                              justify-content: center;
                              min-height: 100vh;
                          }
                          .qr-container {
                              max-width: 400px;
                              margin: 0 auto;
                          }
                          svg { 
                              max-width: 100%; 
                              height: auto; 
                          }
                          h2 { 
                              margin-bottom: 5px; 
                              color: #4a6fa5;
                          }
                          p { 
                              margin-top: 0; 
                              color: #6c757d; 
                          }
                          .patient-info {
                              margin: 20px 0;
                          }
                          @media print {
                              body { padding: 0; }
                              .no-print { display: none; }
                          }
                      </style>
                  </head>
                  <body>
                      <div class="qr-container">
                          <div class="patient-info">
                              <h2><i class="fas fa-user"></i> {{ $selectedPatient->nom_complet }}</h2>
                              <p><i class="fas fa-id-card"></i> ID: {{ $selectedPatient->numero_dossier }}</p>
                          </div>
                          ${qrcodeDiv.innerHTML}
                          <p class="no-print">Scannez ce code pour accéder aux informations du patient</p>
                      </div>
                      <script>
                          window.onload = function() {
                              setTimeout(function() {
                                  window.print();
                                  window.close();
                              }, 200);
                          };
                      </script>
                  </body>
              </html>
          `);
          printWindow.document.close();
      });
      @endif
  }
  
  // Fonction pour afficher des toasts de notification
  function showToast(type, title, message) {
      const toastContainer = document.querySelector('.toast-container');
      if (!toastContainer) return;
      
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      
      toast.innerHTML = `
          <div class="toast-icon">
              <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')}"></i>
          </div>
          <div class="toast-content">
              <div class="toast-title">${title}</div>
              <div class="toast-message">${message}</div>
          </div>
          <button class="toast-close"><i class="fas fa-times"></i></button>
      `;
      
      toastContainer.appendChild(toast);
      
      // Afficher avec transition
      setTimeout(() => {
          toast.classList.add('show');
      }, 10);
      
      // Fermer automatiquement après 5 secondes
      const timeoutId = setTimeout(() => {
          closeToast(toast);
      }, 5000);
      
      // Bouton de fermeture
      const closeButton = toast.querySelector('.toast-close');
      closeButton.addEventListener('click', () => {
          clearTimeout(timeoutId);
          closeToast(toast);
      });
  }
  
  function closeToast(toast) {
      toast.classList.remove('show');
      setTimeout(() => {
          toast.remove();
      }, 300);
  }
  
  // Export des données
  const exportCsvBtn = document.getElementById('export-csv');
  const exportPdfBtn = document.getElementById('export-pdf');
  
  if (exportCsvBtn) {
      exportCsvBtn.addEventListener('click', function() {
          @if($selectedPatient)
          // Afficher un indicateur de chargement
          const originalContent = exportCsvBtn.innerHTML;
          exportCsvBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
          exportCsvBtn.disabled = true;
          
          // Logique d'export CSV
          const data = @json([
              'patient' => $selectedPatient->nom_complet,
              'signesVitaux' => $signesVitaux,
              'laboratoires' => $laboratoires,
              'prescriptions' => $prescriptions
          ]);
          
          try {
              // Création du workbook
              const wb = XLSX.utils.book_new();
              
              // Feuille pour les signes vitaux
              if (data.signesVitaux && data.signesVitaux.length > 0) {
                  const wsVitals = XLSX.utils.json_to_sheet(data.signesVitaux.map(sv => ({
                      'Date': new Date(sv.created_at).toLocaleDateString(),
                      'Fréquence cardiaque (bpm)': sv.rythme_cardiaque,
                      'Pression artérielle (mmHg)': sv.pression_arterielle,
                      'Température (°C)': sv.temperature,
                      'Saturation O2 (%)': sv.saturation_oxygene,
                      'Poids (kg)': sv.poids,
                      'Remarques': sv.remarques || ''
                  })));
                  XLSX.utils.book_append_sheet(wb, wsVitals, "Signes Vitaux");
              }
              
              // Feuille pour les résultats de laboratoire
              if (data.laboratoires && data.laboratoires.length > 0) {
                  const labResults = [];
                  data.laboratoires.forEach(lab => {
                      lab.resultats.forEach(res => {
                          labResults.push({
                              'Date': new Date(lab.date).toLocaleDateString(),
                              'Test': lab.titre,
                              'Paramètre': res.parametre,
                              'Valeur': res.valeur,
                              'Unité': res.unite,
                              'Statut': res.statut,
                              'Laboratoire': lab.laboratoire,
                              'Médecin': lab.medecin
                          });
                      });
                  });
                  
                  if (labResults.length > 0) {
                      const wsLab = XLSX.utils.json_to_sheet(labResults);
                      XLSX.utils.book_append_sheet(wb, wsLab, "Laboratoire");
                  }
              }
              
              // Feuille pour les prescriptions
              if (data.prescriptions && data.prescriptions.length > 0) {
                  const wsPrescriptions = XLSX.utils.json_to_sheet(data.prescriptions.map(p => ({
                      'Date début': new Date(p.date_debut).toLocaleDateString(),
                      'Date fin': new Date(p.date_fin).toLocaleDateString(),
                      'Médicament': p.medicament,
                      'Catégorie': p.categorie,
                      'Posologie': p.posologie,
                      'Statut': p.statut === 'active' ? 'En cours' : 'Terminé',
                      'Médecin': p.medecin,
                      'Remarques': p.remarques || ''
                  })));
                  XLSX.utils.book_append_sheet(wb, wsPrescriptions, "Prescriptions");
              }
              
              // Génération du fichier
              XLSX.writeFile(wb, `rapport-${data.patient.replace(/\s+/g, '-')}-${new Date().toISOString().split('T')[0]}.xlsx`);
              
              showToast('success', 'Export CSV', 'Les données ont été exportées avec succès');
          } catch (error) {
              console.error('Erreur lors de l\'export:', error);
              showToast('error', 'Erreur', 'Une erreur est survenue lors de l\'export');
          } finally {
              // Restaurer le bouton
              exportCsvBtn.innerHTML = originalContent;
              exportCsvBtn.disabled = false;
          }
          @endif
      });
  }
  
  if (exportPdfBtn) {
      exportPdfBtn.addEventListener('click', function() {
          @if($selectedPatient)
          // Afficher un indicateur de chargement
          const originalContent = exportPdfBtn.innerHTML;
          exportPdfBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
          exportPdfBtn.disabled = true;
          
          try {
              // Initialisation de jsPDF
              const { jsPDF } = window.jspdf;
              const doc = new jsPDF({
                  orientation: 'portrait',
                  unit: 'mm'
              });
              
              // Titre du document
              doc.setFont('helvetica', 'bold');
              doc.setFontSize(18);
              doc.setTextColor(74, 111, 165);
              doc.text('Rapport médical', 105, 15, { align: 'center' });
              
              // Informations du patient
              doc.setFont('helvetica', 'normal');
              doc.setFontSize(12);
              doc.setTextColor(0, 0, 0);
              doc.text(`Patient: {{ $selectedPatient->nom_complet }}`, 14, 30);
              doc.text(`ID: {{ $selectedPatient->numero_dossier }}`, 14, 38);
              doc.text(`Date du rapport: ${new Date().toLocaleDateString()}`, 14, 46);
              
              // Ajouter une ligne de séparation
              doc.setDrawColor(74, 111, 165);
              doc.setLineWidth(0.5);
              doc.line(14, 52, 196, 52);
              
              // Tableau des signes vitaux
              doc.setFont('helvetica', 'bold');
              doc.setFontSize(14);
              doc.text('Historique des signes vitaux', 14, 60);
              doc.setFont('helvetica', 'normal');
              
              @if(count($signesVitaux) > 0)
              const vitalsColumns = ["Date", "FC (bpm)", "PA (mmHg)", "Temp (°C)", "SpO2 (%)", "Poids (kg)"];
              const vitalsRows = [];
              
              @foreach($signesVitaux as $signe)
              vitalsRows.push([
                  "{{ $signe->created_at->format('d/m/Y') }}",
                  "{{ $signe->rythme_cardiaque }}",
                  "{{ $signe->pression_arterielle }}",
                  "{{ $signe->temperature }}",
                  "{{ $signe->saturation_oxygene }}",
                  "{{ $signe->poids }}"
              ]);
              @endforeach
              
              doc.autoTable({
                  head: [vitalsColumns],
                  body: vitalsRows,
                  startY: 65,
                  theme: 'grid',
                  headStyles: {
                      fillColor: [74, 111, 165],
                      textColor: [255, 255, 255],
                      fontStyle: 'bold'
                  },
                  alternateRowStyles: {
                      fillColor: [240, 240, 240]
                  },
                  margin: { top: 65 }
              });
              @else
              doc.setFont('helvetica', 'italic');
              doc.text('Aucun signe vital enregistré', 14, 65);
              doc.setFont('helvetica', 'normal');
              @endif
              
              // Ajouter une nouvelle page pour les résultats de laboratoire
              doc.addPage();
              doc.setFont('helvetica', 'bold');
              doc.setFontSize(14);
              doc.text('Résultats de laboratoire', 14, 20);
              doc.setFont('helvetica', 'normal');
              
              @if(count($laboratoires) > 0)
              let startY = 30;
              
              @foreach($laboratoires as $labo)
              // Titre du test
              doc.setFont('helvetica', 'bold');
              doc.text(`• {{ $labo->titre }} ({{ $labo->date->format('d/m/Y') })`, 14, startY);
              doc.setFont('helvetica', 'normal');
              startY += 7;
              
              // Tableau des résultats
              const labColumns = ["Paramètre", "Valeur", "Statut"];
              const labRows = [];
              
              @foreach($labo->resultats as $resultat)
              labRows.push([
                  "{{ $resultat->parametre }}",
                  "{{ $resultat->valeur }} {{ $resultat->unite }}",
                  "{{ ucfirst($resultat->statut) }}"
              ]);
              @endforeach
              
              doc.autoTable({
                  head: [labColumns],
                  body: labRows,
                  startY: startY,
                  theme: 'grid',
                  headStyles: {
                      fillColor: [74, 111, 165],
                      textColor: [255, 255, 255],
                      fontStyle: 'bold'
                  },
                  columnStyles: {
                      2: { cellWidth: 30 }
                  },
                  styles: {
                      fontSize: 10
                  },
                  didDrawCell: (data) => {
                      if (data.section === 'body' && data.column.index === 2) {
                          const value = data.cell.raw;
                          if (value === 'normal') {
                              doc.setTextColor(40, 167, 69);
                          } else if (value === 'abnormal') {
                              doc.setTextColor(220, 53, 69);
                          } else {
                              doc.setTextColor(255, 193, 7);
                          }
                      }
                  }
              });
              
              startY = doc.lastAutoTable.finalY + 10;
              
              // Vérifier si on dépasse la page
              if (startY > 270) {
                  doc.addPage();
                  startY = 20;
              }
              @endforeach
              @else
              doc.setFont('helvetica', 'italic');
              doc.text('Aucun résultat de laboratoire disponible', 14, 30);
              doc.setFont('helvetica', 'normal');
              @endif
              
              // Ajouter une nouvelle page pour les prescriptions
              doc.addPage();
              doc.setFont('helvetica', 'bold');
              doc.setFontSize(14);
              doc.text('Prescriptions médicales', 14, 20);
              doc.setFont('helvetica', 'normal');
              
              @if(count($prescriptions) > 0)
              startY = 30;
              
              @foreach($prescriptions as $prescription)
              // Titre de la prescription
              doc.setFont('helvetica', 'bold');
              doc.text(`• {{ $prescription->medicament }} ({{ $prescription->statut == 'active' ? 'En cours' : 'Terminé' })`, 14, startY);
              doc.setFont('helvetica', 'normal');
              startY += 7;
              
              // Détails
              doc.text(`Catégorie: {{ $prescription->categorie }}`, 20, startY);
              startY += 7;
              doc.text(`Posologie: {{ $prescription->posologie }}`, 20, startY);
              startY += 7;
              doc.text(`Période: du {{ $prescription->date_debut->format('d/m/Y') }} au {{ $prescription->date_fin->format('d/m/Y') }}`, 20, startY);
              startY += 7;
              doc.text(`Prescrit par: {{ $prescription->medecin }}`, 20, startY);
              startY += 10;
              
              // Vérifier si on dépasse la page
              if (startY > 270) {
                  doc.addPage();
                  startY = 20;
              }
              @endforeach
              @else
              doc.setFont('helvetica', 'italic');
              doc.text('Aucune prescription disponible', 14, 30);
              doc.setFont('helvetica', 'normal');
              @endif
              
              // Pied de page
              const pageCount = doc.internal.getNumberOfPages();
              for (let i = 1; i <= pageCount; i++) {
                  doc.setPage(i);
                  doc.setFontSize(10);
                  doc.setTextColor(108, 117, 125);
                  doc.text(`Page ${i} sur ${pageCount}`, 105, 287, { align: 'center' });
                  doc.text(`Généré le ${new Date().toLocaleDateString()}`, 195, 287, { align: 'right' });
              }
              
              // Téléchargement du PDF
              doc.save(`rapport-{{ $selectedPatient->numero_dossier }}-${new Date().toISOString().split('T')[0]}.pdf`);
              
              showToast('success', 'Export PDF', 'Les données ont été exportées avec succès');
          } catch (error) {
              console.error('Erreur lors de l\'export PDF:', error);
              showToast('error', 'Erreur', 'Une erreur est survenue lors de l\'export');
          } finally {
              // Restaurer le bouton
              exportPdfBtn.innerHTML = originalContent;
              exportPdfBtn.disabled = false;
          }
          @endif
      });
  }
  
  // Afficher les données du patient si un patient est sélectionné
  @if($selectedPatient)
  document.getElementById('patient-data').style.display = 'block';
  document.getElementById('no-patient-message').style.display = 'none';
  @endif
  
  // Animation pour les cartes de métriques
  const metricCards = document.querySelectorAll('.metric-card');
  metricCards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.1}s`;
  });
});