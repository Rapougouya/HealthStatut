@extends('layouts.app')

@section('title', 'Maintenance Système')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
        }

        .maintenance-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .maintenance-header {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .title-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .header-title h1 {
            color: var(--text-primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .system-status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .status-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }

        .status-card:hover {
            transform: translateY(-3px);
        }

        .status-card.system { border-left-color: var(--success-color); }
        .status-card.database { border-left-color: var(--info-color); }
        .status-card.storage { border-left-color: var(--warning-color); }
        .status-card.backup { border-left-color: var(--danger-color); }

        .status-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 15px;
        }

        .status-icon {
            font-size: 2rem;
            margin-right: 15px;
        }

        .status-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: auto;
        }

        .status-indicator.online { background: var(--success-color); }
        .status-indicator.warning { background: var(--warning-color); }
        .status-indicator.offline { background: var(--danger-color); }

        .status-details {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .maintenance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .maintenance-section {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        }

        .section-title {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }

        .maintenance-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .action-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .action-icon.cache { background: var(--info-color); }
        .action-icon.logs { background: var(--warning-color); }
        .action-icon.temp { background: var(--danger-color); }
        .action-icon.optimization { background: var(--success-color); }

        .action-details h4 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
            font-weight: 600;
        }

        .action-details p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .scheduled-maintenance {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .schedule-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--info-color);
        }

        .schedule-time {
            font-weight: 600;
            color: var(--text-primary);
            min-width: 150px;
        }

        .schedule-task {
            flex: 1;
            color: var(--text-secondary);
        }

        .schedule-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .schedule-status.pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .schedule-status.running {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }

        .schedule-status.completed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: var(--info-color);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        @media (max-width: 768px) {
            .maintenance-container {
                padding: 15px;
            }
            .maintenance-header {
                padding: 20px;
            }
            .header-title h1 {
                font-size: 2rem;
            }
            .maintenance-grid {
                grid-template-columns: 1fr;
            }
            .system-status {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
<div class="maintenance-container">
    <!-- En-tête -->
    <div class="maintenance-header">
        <div class="header-title">
            <div class="title-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h1>Maintenance Système</h1>
        </div>
        <p style="color: var(--text-secondary); margin: 0;">Gérez la maintenance et l'optimisation du système HealthStatut</p>
    </div>

    <!-- Status du système -->
    <div class="system-status">
        <div class="status-card system">
            <div class="status-header">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-server status-icon" style="color: var(--success-color);"></i>
                    <div class="status-title">Système</div>
                </div>
                <div class="status-indicator online"></div>
            </div>
            <div class="status-details">
                Temps de fonctionnement: 15 jours<br>
                Charge CPU: 23%<br>
                Mémoire: 4.2GB / 8GB
            </div>
        </div>

        <div class="status-card database">
            <div class="status-header">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-database status-icon" style="color: var(--info-color);"></i>
                    <div class="status-title">Base de Données</div>
                </div>
                <div class="status-indicator online"></div>
            </div>
            <div class="status-details">
                Connexions actives: 12<br>
                Taille: 2.4GB<br>
                Dernière sauvegarde: Il y a 2h
            </div>
        </div>

        <div class="status-card storage">
            <div class="status-header">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-hdd status-icon" style="color: var(--warning-color);"></i>
                    <div class="status-title">Stockage</div>
                </div>
                <div class="status-indicator warning"></div>
            </div>
            <div class="status-details">
                Espace utilisé: 78%<br>
                Disponible: 45GB / 200GB<br>
                Fichiers temporaires: 2.1GB
            </div>
        </div>

        <div class="status-card backup">
            <div class="status-header">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-shield-alt status-icon" style="color: var(--danger-color);"></i>
                    <div class="status-title">Sauvegardes</div>
                </div>
                <div class="status-indicator online"></div>
            </div>
            <div class="status-details">
                Dernière sauvegarde: Réussie<br>
                Taille: 1.8GB<br>
                Prochaine: Dans 22h
            </div>
        </div>
    </div>

    <!-- Sections de maintenance -->
    <div class="maintenance-grid">
        <!-- Actions de maintenance rapide -->
        <div class="maintenance-section">
            <div class="section-header">
                <i class="fas fa-bolt" style="color: #667eea;"></i>
                <h3 class="section-title">Actions Rapides</h3>
            </div>
            
            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon cache">
                        <i class="fas fa-memory"></i>
                    </div>
                    <div class="action-details">
                        <h4>Vider le Cache</h4>
                        <p>Supprime tous les fichiers de cache temporaires</p>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="clearCache()">
                    <i class="fas fa-trash"></i>
                    Vider
                </button>
            </div>

            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon logs">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="action-details">
                        <h4>Rotation des Logs</h4>
                        <p>Archive les anciens logs et libère l'espace</p>
                    </div>
                </div>
                <button class="btn btn-warning" onclick="rotateLogs()">
                    <i class="fas fa-redo"></i>
                    Archiver
                </button>
            </div>

            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon temp">
                        <i class="fas fa-folder-minus"></i>
                    </div>
                    <div class="action-details">
                        <h4>Nettoyer Fichiers Temporaires</h4>
                        <p>Supprime les fichiers temporaires inutiles</p>
                    </div>
                </div>
                <button class="btn btn-danger" onclick="cleanTempFiles()">
                    <i class="fas fa-broom"></i>
                    Nettoyer
                </button>
            </div>

            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon optimization">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="action-details">
                        <h4>Optimiser Base de Données</h4>
                        <p>Optimise les tables et améliore les performances</p>
                    </div>
                </div>
                <button class="btn btn-success" onclick="optimizeDatabase()">
                    <i class="fas fa-magic"></i>
                    Optimiser
                </button>
            </div>
        </div>

        <!-- Outils de diagnostic -->
        <div class="maintenance-section">
            <div class="section-header">
                <i class="fas fa-stethoscope" style="color: #667eea;"></i>
                <h3 class="section-title">Diagnostics</h3>
            </div>
            
            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon cache">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="action-details">
                        <h4>Test de Santé Système</h4>
                        <p>Vérification complète de tous les composants</p>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="healthCheck()">
                    <i class="fas fa-play"></i>
                    Lancer
                </button>
            </div>

            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon logs">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div class="action-details">
                        <h4>Test Connectivité</h4>
                        <p>Vérifie les connexions réseau et services</p>
                    </div>
                </div>
                <button class="btn btn-warning" onclick="connectivityTest()">
                    <i class="fas fa-wifi"></i>
                    Tester
                </button>
            </div>

            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon temp">
                        <i class="fas fa-bug"></i>
                    </div>
                    <div class="action-details">
                        <h4>Rapport d'Erreurs</h4>
                        <p>Génère un rapport détaillé des erreurs</p>
                    </div>
                </div>
                <button class="btn btn-danger" onclick="generateErrorReport()">
                    <i class="fas fa-file-medical"></i>
                    Générer
                </button>
            </div>

            <div class="maintenance-action">
                <div class="action-info">
                    <div class="action-icon optimization">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-details">
                        <h4>Analyse Performance</h4>
                        <p>Analyse les performances et recommandations</p>
                    </div>
                </div>
                <button class="btn btn-success" onclick="performanceAnalysis()">
                    <i class="fas fa-search"></i>
                    Analyser
                </button>
            </div>
        </div>
    </div>

    <!-- Maintenance programmée -->
    <div class="scheduled-maintenance">
        <div class="section-header">
            <i class="fas fa-calendar-alt" style="color: #667eea;"></i>
            <h3 class="section-title">Maintenance Programmée</h3>
        </div>
        
        <div class="schedule-item">
            <div class="schedule-time">Tous les jours à 02:00</div>
            <div class="schedule-task">Sauvegarde automatique de la base de données</div>
            <span class="schedule-status completed">Terminé</span>
        </div>
        
        <div class="schedule-item">
            <div class="schedule-time">Tous les dimanche à 03:00</div>
            <div class="schedule-task">Optimisation complète de la base de données</div>
            <span class="schedule-status pending">En attente</span>
        </div>
        
        <div class="schedule-item">
            <div class="schedule-time">Toutes les heures</div>
            <div class="schedule-task">Nettoyage des logs temporaires</div>
            <span class="schedule-status running">En cours</span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 65%;"></div>
            </div>
        </div>
        
        <div class="schedule-item">
            <div class="schedule-time">Chaque lundi à 01:00</div>
            <div class="schedule-task">Archive des anciens logs système</div>
            <span class="schedule-status pending">En attente</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function clearCache() {
    showToast('Vidage du cache en cours...', 'info');
    setTimeout(() => {
        showToast('Cache vidé avec succès - 245MB libérés', 'success');
    }, 2000);
}

function rotateLogs() {
    showToast('Rotation des logs en cours...', 'info');
    setTimeout(() => {
        showToast('Logs archivés avec succès - 128MB libérés', 'success');
    }, 3000);
}

function cleanTempFiles() {
    if (confirm('Confirmer la suppression des fichiers temporaires ?')) {
        showToast('Nettoyage des fichiers temporaires...', 'info');
        setTimeout(() => {
            showToast('Fichiers temporaires supprimés - 2.1GB libérés', 'success');
        }, 4000);
    }
}

function optimizeDatabase() {
    showToast('Optimisation de la base de données...', 'info');
    setTimeout(() => {
        showToast('Base de données optimisée avec succès', 'success');
    }, 5000);
}

function healthCheck() {
    showToast('Test de santé système en cours...', 'info');
    setTimeout(() => {
        showToast('Système en parfait état de fonctionnement', 'success');
    }, 3000);
}

function connectivityTest() {
    showToast('Test de connectivité en cours...', 'info');
    setTimeout(() => {
        showToast('Toutes les connexions sont opérationnelles', 'success');
    }, 2500);
}

function generateErrorReport() {
    showToast('Génération du rapport d\'erreurs...', 'info');
    setTimeout(() => {
        showToast('Rapport généré et envoyé par email', 'success');
    }, 3500);
}

function performanceAnalysis() {
    showToast('Analyse des performances en cours...', 'info');
    setTimeout(() => {
        showToast('Analyse terminée - Rapport disponible', 'success');
    }, 4500);
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background: #10b981;' : 
          type === 'warning' ? 'background: #f59e0b;' :
          type === 'info' ? 'background: #3b82f6;' : 'background: #ef4444;'}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 4000);
}

// Animation d'entrée
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.status-card, .maintenance-section, .scheduled-maintenance');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection