document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les onglets
    initTabs();
    
    // Initialiser les événements de filtre
    initFilters();
    
    // Initialiser la pagination
    initPagination();
    
    // Charger les alertes
    loadAlerts();
    
    // Initialiser les événements pour les boutons d'action
    initActionButtons();
    
    // Initialiser le modal
    initModal();
});

function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Désactiver tous les onglets
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activer l'onglet cliqué
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
}

function initFilters() {
    const applyButton = document.querySelector('.apply-btn');
    if (applyButton) {
        applyButton.addEventListener('click', () => {
            loadAlerts(); // Recharger les alertes avec les filtres appliqués
        });
    }
    
    // Initialiser la recherche
    const searchButton = document.querySelector('.search-group button');
    if (searchButton) {
        searchButton.addEventListener('click', () => {
            loadAlerts(); // Recharger les alertes avec la recherche appliquée
        });
    }
}

function initPagination() {
    const prevButton = document.querySelector('.pagination-btn[data-action="prev"]');
    const nextButton = document.querySelector('.pagination-btn[data-action="next"]');
    
    if (prevButton) {
        prevButton.addEventListener('click', () => {
            const currentPage = parseInt(document.querySelector('.page-info').getAttribute('data-current-page'));
            if (currentPage > 1) {
                navigateToPage(currentPage - 1);
            }
        });
    }
    
    if (nextButton) {
        nextButton.addEventListener('click', () => {
            const currentPage = parseInt(document.querySelector('.page-info').getAttribute('data-current-page'));
            const totalPages = parseInt(document.querySelector('.page-info').getAttribute('data-total-pages'));
            if (currentPage < totalPages) {
                navigateToPage(currentPage + 1);
            }
        });
    }
}

function navigateToPage(page) {
    // Mettre à jour la page courante
    document.querySelector('.page-info').setAttribute('data-current-page', page);
    
    // Recharger les alertes pour la nouvelle page
    loadAlerts();
    
    // Mettre à jour le texte de la pagination
    updatePaginationText();
}

function updatePaginationText() {
    const pageInfo = document.querySelector('.page-info');
    const currentPage = parseInt(pageInfo.getAttribute('data-current-page'));
    const totalPages = parseInt(pageInfo.getAttribute('data-total-pages'));
    pageInfo.textContent = `Page ${currentPage} sur ${totalPages}`;
}

function loadAlerts() {
    // Simulation de chargement des alertes (à remplacer par une vraie API)
    const alertsList = document.querySelector('.alerts-list');
    
    // Récupérer les valeurs des filtres
    const severity = document.querySelector('select[name="severity"]').value;
    const dateFrom = document.querySelector('input[name="date-from"]').value;
    const dateTo = document.querySelector('input[name="date-to"]').value;
    const searchQuery = document.querySelector('.search-group input').value;
    
    // Pour la démo, générer des alertes aléatoires
    const mockAlerts = generateMockAlerts(10);
    
    // Vider la liste d'alertes
    alertsList.innerHTML = '';
    
    // Ajouter les alertes à la liste
    mockAlerts.forEach(alert => {
        alertsList.appendChild(createAlertItem(alert));
    });
    
    // Mettre à jour les compteurs d'alertes
    document.querySelector('.active-alerts .count').textContent = mockAlerts.filter(a => !a.resolved).length;
    document.querySelector('.resolved-alerts .count').textContent = mockAlerts.filter(a => a.resolved).length;
    
    // Mettre à jour la pagination
    document.querySelector('.page-info').setAttribute('data-total-pages', '5');
    updatePaginationText();
}

function generateMockAlerts(count) {
    const severities = ['critical', 'high', 'medium', 'low'];
    const messages = [
        'Fréquence cardiaque élevée',
        'Pression artérielle basse',
        'Niveau d\'oxygène critique',
        'Température corporelle élevée',
        'Glycémie basse'
    ];
    
    const alerts = [];
    
    for (let i = 0; i < count; i++) {
        const severity = severities[Math.floor(Math.random() * severities.length)];
        const message = messages[Math.floor(Math.random() * messages.length)];
        const timestamp = new Date();
        timestamp.setHours(timestamp.getHours() - Math.floor(Math.random() * 24));
        
        alerts.push({
            id: i + 1,
            title: `Alerte patient #${Math.floor(Math.random() * 1000)}`,
            message: message,
            severity: severity,
            timestamp: timestamp.toLocaleString(),
            resolved: Math.random() > 0.7
        });
    }
    
    return alerts;
}

function createAlertItem(alert) {
    const alertItem = document.createElement('div');
    alertItem.className = `alert-item alert-${alert.severity}`;
    alertItem.setAttribute('data-alert-id', alert.id);
    
    alertItem.innerHTML = `
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-info">
            <div class="alert-details">
                <div class="alert-title">${alert.title}</div>
                <div class="alert-message">${alert.message}</div>
            </div>
        </div>
        <div class="alert-meta">
            <div class="alert-timestamp">${alert.timestamp}</div>
            <div class="alert-severity severity-${alert.severity}">${alert.severity}</div>
        </div>
        <div class="alert-actions">
            <button class="alert-btn view-btn" title="Voir les détails">
                <i class="fas fa-eye"></i>
            </button>
            <button class="alert-btn resolve-btn" title="Marquer comme résolu">
                <i class="fas fa-check"></i>
            </button>
        </div>
    `;
    
    // Ajouter des écouteurs d'événements
    alertItem.querySelector('.view-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        openAlertDetails(alert);
    });
    
    alertItem.querySelector('.resolve-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        resolveAlert(alert.id);
    });
    
    alertItem.addEventListener('click', () => {
        openAlertDetails(alert);
    });
    
    return alertItem;
}

function openAlertDetails(alert) {
    const modal = document.querySelector('.alert-details-modal');
    const modalTitle = modal.querySelector('.modal-header h3');
    const modalBody = modal.querySelector('.modal-body');
    
    modalTitle.textContent = alert.title;
    
    modalBody.innerHTML = `
        <div class="mb-4">
            <p class="text-lg mb-2">${alert.message}</p>
            <div class="text-sm text-gray-500">${alert.timestamp}</div>
            <div class="mt-2 inline-block alert-severity severity-${alert.severity}">${alert.severity}</div>
        </div>
        <div class="alert-patient-info mb-4">
            <h4 class="font-bold mb-2">Informations du patient</h4>
            <p>Patient ID: P-${Math.floor(Math.random() * 10000)}</p>
            <p>Nom: Jean Dupont</p>
            <p>Age: 65 ans</p>
        </div>
        <div class="alert-vital-signs">
            <h4 class="font-bold mb-2">Signes vitaux</h4>
            <div class="grid grid-cols-2 gap-2">
                <div>Fréquence cardiaque: <span class="font-semibold">95 bpm</span></div>
                <div>Pression artérielle: <span class="font-semibold">135/85 mmHg</span></div>
                <div>Niveau d'oxygène: <span class="font-semibold">94%</span></div>
                <div>Température: <span class="font-semibold">38.5°C</span></div>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

function resolveAlert(alertId) {
    // Simulation de résolution d'alerte
    console.log(`Résolution de l'alerte ${alertId}`);
    
    // Dans une vraie application, cela ferait un appel API
    // puis rechargerait les données
    
    // Pour la démo, masquer simplement l'élément
    const alertItem = document.querySelector(`.alert-item[data-alert-id="${alertId}"]`);
    if (alertItem) {
        alertItem.style.opacity = '0.5';
        alertItem.style.pointerEvents = 'none';
        
        // Mettre à jour les compteurs
        const activeCount = parseInt(document.querySelector('.active-alerts .count').textContent);
        const resolvedCount = parseInt(document.querySelector('.resolved-alerts .count').textContent);
        
        document.querySelector('.active-alerts .count').textContent = activeCount - 1;
        document.querySelector('.resolved-alerts .count').textContent = resolvedCount + 1;
    }
}

function initActionButtons() {
    // Initialiser le bouton "Résoudre toutes"
    const resolveAllBtn = document.querySelector('.resolve-all-btn');
    if (resolveAllBtn) {
        resolveAllBtn.addEventListener('click', () => {
            const activeAlerts = document.querySelectorAll('.alert-item');
            activeAlerts.forEach(alert => {
                alert.style.opacity = '0.5';
                alert.style.pointerEvents = 'none';
            });
            
            document.querySelector('.active-alerts .count').textContent = '0';
            
            // Afficher une notification
            showToast('Toutes les alertes ont été résolues', 'success');
        });
    }
    
    // Initialiser le bouton "Paramètres"
    const settingsBtn = document.querySelector('.settings-btn');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', () => {
            // Activer l'onglet de paramètres
            document.querySelector('.tab-btn[data-tab="settings-tab"]').click();
        });
    }
    
    // Initialiser les boutons de sauvegarde des paramètres
    const saveBtn = document.querySelector('.save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            // Simuler la sauvegarde des paramètres
            showToast('Paramètres enregistrés avec succès', 'success');
        });
    }
}

function initModal() {
    const modal = document.querySelector('.alert-details-modal');
    const closeButtons = modal.querySelectorAll('.close-modal, .close-btn');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    });
    
    // Fermer le modal en cliquant à l'extérieur
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Bouton de résolution dans le modal
    const resolveBtn = modal.querySelector('.resolve-btn');
    if (resolveBtn) {
        resolveBtn.addEventListener('click', () => {
            const alertId = modal.getAttribute('data-alert-id');
            resolveAlert(alertId);
            modal.style.display = 'none';
            
            showToast('Alerte résolue avec succès', 'success');
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'success' ? 'toast-success' : 'toast-alert'} show`;
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${type === 'success' ? 'Succès' : 'Information'}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Ajouter le toast au conteneur
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        container.appendChild(toast);
    } else {
        toastContainer.appendChild(toast);
    }
    
    // Fermer le toast en cliquant sur le bouton de fermeture
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    });
    
    // Fermer automatiquement après 5 secondes
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 5000);
}
