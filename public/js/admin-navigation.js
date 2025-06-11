// Navigation pour l'administration système
function openSystemSettings() {
    // Rediriger vers la page de configuration serveur
    window.location.href = '/admin/system/server-config';
}

function openSystemLogs() {
    window.location.href = '/admin/system/logs';
}

function openSystemMaintenance() {
    window.location.href = '/admin/system/maintenance';
}

function openSystemExport() {
    window.location.href = '/admin/system/export-data';
}

// Fonction générique pour naviguer vers les pages système
function navigateToSystemPage(page) {
    const routes = {
        'server-config': '/admin/system/server-config',
        'logs': '/admin/system/logs',
        'maintenance': '/admin/system/maintenance',
        'export-data': '/admin/system/export-data'
    };
    
    if (routes[page]) {
        window.location.href = routes[page];
    } else {
        console.error('Page système inconnue:', page);
    }
}

// Event listeners pour les boutons de navigation système
document.addEventListener('DOMContentLoaded', function() {
    // Bouton Configuration Serveur
    const serverConfigBtn = document.querySelector('[data-action="server-config"], .btn:has(i.fas.fa-server)');
    if (serverConfigBtn) {
        serverConfigBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('server-config');
        });
    }
    
    // Bouton Logs Système
    const logsBtn = document.querySelector('[data-action="system-logs"], button:has(i.fas.fa-file-alt), .btn:has(i.fas.fa-file-alt)');
    if (logsBtn) {
        logsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('logs');
        });
    }
    
    // Bouton Maintenance
    const maintenanceBtn = document.querySelector('[data-action="maintenance"], button:has(i.fas.fa-tools), .btn:has(i.fas.fa-tools)');
    if (maintenanceBtn) {
        maintenanceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('maintenance');
        });
    }
    
    // Bouton Export Données
    const exportBtn = document.querySelector('[data-action="export-data"], button:has(i.fas.fa-download), .btn:has(i.fas.fa-download)');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('export-data');
        });
    }

    // Support pour les boutons avec onclick spécifique
    const viewSystemLogsBtn = document.querySelector('button[onclick="viewSystemLogs()"]');
    if (viewSystemLogsBtn) {
        viewSystemLogsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('logs');
        });
    }

    const systemMaintenanceBtn = document.querySelector('button[onclick="systemMaintenance()"]');
    if (systemMaintenanceBtn) {
        systemMaintenanceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('maintenance');
        });
    }

    const exportDataBtn = document.querySelector('button[onclick="exportData()"]');
    if (exportDataBtn) {
        exportDataBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('export-data');
        });
    }
});

// Ajouter des fonctions globales pour la compatibilité avec les onclick inline
function viewSystemLogs() {
    navigateToSystemPage('logs');
}

function systemMaintenance() {
    navigateToSystemPage('maintenance');
}

function exportData() {
    navigateToSystemPage('export-data');
}
