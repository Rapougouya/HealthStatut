// Scripts améliorés pour les rapports avec export et QR
document.addEventListener('DOMContentLoaded', function() {
    initializeReportsPage();
});

function initializeReportsPage() {
    console.log('Initialisation de la page des rapports améliorée');
    
    // Initialiser les boutons d'export
    const exportCsvBtn = document.getElementById('export-csv');
    const exportPdfBtn = document.getElementById('export-pdf');
    const scanQrBtn = document.getElementById('scan-qr');
    const generateQrBtn = document.getElementById('generate-qr');
    
    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', exportToCSV);
    }
    
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', exportToPDF);
    }
    
    if (scanQrBtn) {
        scanQrBtn.addEventListener('click', openQRScanner);
    }
    
    if (generateQrBtn) {
        generateQrBtn.addEventListener('click', openQRGenerator);
    }
    
    // Initialiser les modals
    initializeModals();
    
    // Initialiser les toasts
    initializeToasts();
}

// Export CSV
function exportToCSV() {
    console.log('Export CSV démarré');
    
    // Récupérer les données du tableau
    const table = document.querySelector('.reports-table');
    if (!table) {
        showToast('Aucune donnée à exporter', 'error');
        return;
    }
    
    const rows = table.querySelectorAll('tr');
    const csvData = [];
    
    // En-têtes
    const headers = [];
    rows[0].querySelectorAll('th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csvData.push(headers.join(','));
    
    // Données
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = [];
        row.querySelectorAll('td').forEach((td, index) => {
            if (index < headers.length - 1) { // Exclure la colonne Actions
                let cellText = td.textContent.trim();
                // Échapper les virgules et guillemets
                if (cellText.includes(',') || cellText.includes('"')) {
                    cellText = '"' + cellText.replace(/"/g, '""') + '"';
                }
                cells.push(cellText);
            }
        });
        csvData.push(cells.join(','));
    }
    
    // Créer le fichier CSV
    const csvContent = csvData.join('\n');
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `rapports_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast('Export CSV réussi', 'success');
    } else {
        showToast('Export non supporté par ce navigateur', 'error');
    }
}

// Export PDF (nécessite jsPDF)
function exportToPDF() {
    console.log('Export PDF démarré');
    
    // Vérifier si jsPDF est disponible
    if (typeof window.jspdf === 'undefined') {
        showToast('Librairie PDF non chargée', 'error');
        return;
    }
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // En-tête du document
    doc.setFontSize(20);
    doc.text('Rapport Médical HealthMobis', 105, 20, { align: 'center' });
    
    doc.setFontSize(12);
    doc.text('Système de Monitoring Médical', 105, 30, { align: 'center' });
    
    // Date de génération
    doc.setFontSize(10);
    doc.text(`Généré le: ${new Date().toLocaleDateString('fr-FR')}`, 20, 45);
    
    // Récupérer les données du tableau
    const table = document.querySelector('.reports-table');
    if (!table) {
        showToast('Aucune donnée à exporter', 'error');
        return;
    }
    
    // Préparer les données pour le tableau PDF
    const headers = [];
    const rows = table.querySelectorAll('tr');
    
    // En-têtes (exclure Actions)
    rows[0].querySelectorAll('th').forEach((th, index) => {
        if (th.textContent.trim() !== 'Actions') {
            headers.push(th.textContent.trim());
        }
    });
    
    // Données
    const data = [];
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const rowData = [];
        row.querySelectorAll('td').forEach((td, index) => {
            if (index < headers.length) {
                rowData.push(td.textContent.trim());
            }
        });
        data.push(rowData);
    }
    
    // Ajouter le tableau au PDF (utilisation simple)
    let yPosition = 60;
    const lineHeight = 8;
    const pageHeight = 280;
    
    // En-têtes
    doc.setFontSize(10);
    doc.setFont(undefined, 'bold');
    let xPosition = 20;
    headers.forEach(header => {
        doc.text(header, xPosition, yPosition);
        xPosition += 35;
    });
    
    yPosition += lineHeight;
    doc.line(20, yPosition, 190, yPosition); // Ligne de séparation
    yPosition += 5;
    
    // Données
    doc.setFont(undefined, 'normal');
    data.forEach(row => {
        if (yPosition > pageHeight) {
            doc.addPage();
            yPosition = 20;
        }
        
        xPosition = 20;
        row.forEach(cell => {
            doc.text(cell.substring(0, 15), xPosition, yPosition); // Limiter la longueur
            xPosition += 35;
        });
        yPosition += lineHeight;
    });
    
    // Pied de page
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.text(`Page ${i} sur ${pageCount}`, 105, 290, { align: 'center' });
    }
    
    // Sauvegarder
    doc.save(`rapport_medical_${new Date().toISOString().split('T')[0]}.pdf`);
    showToast('Export PDF réussi', 'success');
}

// Scanner QR
function openQRScanner() {
    console.log('Ouverture du scanner QR');
    const modal = document.getElementById('qr-scan-modal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    
    // Démarrer la caméra
    startCamera();
}

function startCamera() {
    const video = document.getElementById('qr-video');
    const placeholder = document.getElementById('scanner-placeholder');
    
    if (!video || !placeholder) return;
    
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment',
            width: { ideal: 640 },
            height: { ideal: 480 }
        } 
    })
    .then(stream => {
        video.srcObject = stream;
        video.style.display = 'block';
        placeholder.style.display = 'none';
        
        // Démarrer l'animation de scan
        const scanningLine = document.querySelector('.scanning-line');
        if (scanningLine) {
            scanningLine.style.display = 'block';
        }
        
        showToast('Caméra activée', 'success');
        
        // Simulation d'un scan réussi après 3 secondes
        setTimeout(() => {
            simulateQRScan();
        }, 3000);
    })
    .catch(err => {
        console.error('Erreur caméra:', err);
        placeholder.style.display = 'flex';
        video.style.display = 'none';
        showToast('Erreur d\'accès à la caméra', 'error');
    });
}

function simulateQRScan() {
    // Simulation d'un scan réussi
    const mockData = {
        id: 'R-001',
        patient: 'Jean Dupont',
        type: 'medical',
        date: '2024-05-27'
    };
    
    // Fermer le scanner
    closeQRScanner();
    
    // Afficher les informations récupérées
    showToast(`Patient trouvé: ${mockData.patient}`, 'success');
    
    // Vous pourriez ici rediriger vers la page du patient ou afficher ses données
    console.log('Données QR scannées:', mockData);
}

function closeQRScanner() {
    const modal = document.getElementById('qr-scan-modal');
    const video = document.getElementById('qr-video');
    
    if (video && video.srcObject) {
        const tracks = video.srcObject.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }
    
    if (modal) {
        modal.style.display = 'none';
    }
}

// Générateur QR
function openQRGenerator() {
    console.log('Ouverture du générateur QR');
    
    // Pour l'exemple, utiliser les données du premier rapport visible
    const firstRow = document.querySelector('.reports-table tbody tr');
    if (!firstRow) {
        showToast('Aucun rapport sélectionné', 'warning');
        return;
    }
    
    const cells = firstRow.querySelectorAll('td');
    const reportData = {
        id: cells[0]?.textContent.trim() || 'R-001',
        patient: cells[1]?.textContent.trim() || 'Patient Inconnu',
        type: cells[2]?.textContent.trim() || 'medical',
        date: cells[3]?.textContent.trim() || new Date().toLocaleDateString()
    };
    
    generateQRCode(reportData);
    
    const modal = document.getElementById('qr-generate-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function generateQRCode(data) {
    const canvas = document.getElementById('qr-canvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const size = 200;
    
    canvas.width = size;
    canvas.height = size;
    
    // Fond blanc
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, size, size);
    
    // Bordure
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.strokeRect(10, 10, size - 20, size - 20);
    
    // Motif QR simplifié (dans un vrai projet, utilisez une librairie QR)
    ctx.fillStyle = '#000000';
    const blockSize = 8;
    const margin = 20;
    
    // Générer un motif pseudo-aléatoire basé sur les données
    const dataString = JSON.stringify(data);
    let seed = 0;
    for (let i = 0; i < dataString.length; i++) {
        seed += dataString.charCodeAt(i);
    }
    
    // Motif de base
    for (let x = margin; x < size - margin; x += blockSize) {
        for (let y = margin; y < size - margin; y += blockSize) {
            seed = (seed * 9301 + 49297) % 233280;
            if (seed / 233280 > 0.5) {
                ctx.fillRect(x, y, blockSize - 1, blockSize - 1);
            }
        }
    }
    
    // Coins de détection
    drawDetectionCorner(ctx, margin, margin);
    drawDetectionCorner(ctx, size - margin - 40, margin);
    drawDetectionCorner(ctx, margin, size - margin - 40);
    
    // Mettre à jour les informations
    document.getElementById('qr-patient-name').textContent = data.patient;
    document.getElementById('qr-patient-id').textContent = `ID: ${data.id}`;
    document.getElementById('qr-report-type').textContent = `Type: ${data.type}`;
    document.getElementById('qr-report-date').textContent = `Date: ${data.date}`;
}

function drawDetectionCorner(ctx, x, y) {
    const size = 40;
    
    // Carré extérieur
    ctx.fillStyle = '#000000';
    ctx.fillRect(x, y, size, size);
    
    // Carré blanc intérieur
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(x + 8, y + 8, size - 16, size - 16);
    
    // Point central
    ctx.fillStyle = '#000000';
    ctx.fillRect(x + 16, y + 16, size - 32, size - 32);
}

function downloadQR() {
    const canvas = document.getElementById('qr-canvas');
    if (!canvas) return;
    
    const link = document.createElement('a');
    link.download = `qr-code-${new Date().getTime()}.png`;
    link.href = canvas.toDataURL();
    link.click();
    
    showToast('QR Code téléchargé', 'success');
}

function printQR() {
    const canvas = document.getElementById('qr-canvas');
    if (!canvas) return;
    
    const dataUrl = canvas.toDataURL();
    const patientName = document.getElementById('qr-patient-name').textContent;
    const patientId = document.getElementById('qr-patient-id').textContent;
    
    const printWindow = window.open('', '', 'width=600,height=600');
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code - ${patientName}</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 50px;
                        margin: 0;
                    }
                    .qr-print-container { 
                        page-break-inside: avoid;
                    }
                    .qr-image {
                        border: 3px solid #000;
                        padding: 20px;
                        margin: 20px auto;
                        background: white;
                        display: inline-block;
                    }
                    .patient-info { 
                        margin-top: 30px; 
                        font-size: 16px;
                        line-height: 1.5;
                    }
                    h2 {
                        color: #333;
                        margin-bottom: 30px;
                    }
                </style>
            </head>
            <body>
                <div class="qr-print-container">
                    <h2>HealthMobis - Code Patient</h2>
                    <div class="qr-image">
                        <img src="${dataUrl}" alt="QR Code" />
                    </div>
                    <div class="patient-info">
                        <p><strong>${patientName}</strong></p>
                        <p>${patientId}</p>
                        <p>Scanné le: ${new Date().toLocaleDateString('fr-FR')}</p>
                    </div>
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
    
    showToast('Impression en cours', 'info');
}

// Gestion des modals
function initializeModals() {
    // Boutons de fermeture
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-overlay');
            if (modal) {
                modal.style.display = 'none';
                
                // Arrêter la caméra si c'est le modal scanner
                if (modal.id === 'qr-scan-modal') {
                    closeQRScanner();
                }
            }
        });
    });
    
    // Fermeture en cliquant à l'extérieur
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                
                // Arrêter la caméra si c'est le modal scanner
                if (this.id === 'qr-scan-modal') {
                    closeQRScanner();
                }
            }
        });
    });
}

// Système de notifications toast
function initializeToasts() {
    // Créer le conteneur de toasts s'il n'existe pas
    if (!document.querySelector('.toast-container')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
}

function showToast(message, type = 'info') {
    const container = document.querySelector('.toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas ${icons[type] || icons.info}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Gestion de la fermeture
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
        removeToast(toast);
    });
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        removeToast(toast);
    }, 5000);
}

function removeToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Fonctions utilitaires
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function formatStatus(status) {
    const statusMap = {
        'completed': 'Complété',
        'pending': 'En attente',
        'processing': 'En cours'
    };
    return statusMap[status] || status;
}

console.log('Scripts de rapports améliorés chargés');