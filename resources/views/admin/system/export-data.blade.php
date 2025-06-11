@extends('layouts.app')

@section('title', 'Export des Données')

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

        .export-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .export-header {
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

        .export-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.patients { border-left-color: var(--info-color); }
        .stat-card.sensors { border-left-color: var(--success-color); }
        .stat-card.logs { border-left-color: var(--warning-color); }
        .stat-card.reports { border-left-color: var(--danger-color); }

        .export-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .export-section {
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

        .export-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        .export-option:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .option-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .option-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
        }

        .option-icon.patients { background: var(--info-color); }
        .option-icon.sensors { background: var(--success-color); }
        .option-icon.alerts { background: var(--danger-color); }
        .option-icon.reports { background: var(--warning-color); }
        .option-icon.logs { background: var(--text-secondary); }
        .option-icon.users { background: #8b5cf6; }

        .option-details h4 {
            margin: 0 0 8px 0;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .option-details p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .data-count {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.85rem;
        }

        .format-selector {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .format-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid rgba(102, 126, 234, 0.2);
            background: white;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .format-btn.active {
            background: var(--info-color);
            color: white;
            border-color: var(--info-color);
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
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

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .export-filters {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .filter-input, .filter-select {
            padding: 10px 12px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .export-history {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 10px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--info-color);
        }

        .history-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .history-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            background: var(--info-color);
        }

        .history-details h5 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
            font-weight: 600;
        }

        .history-details span {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 10px 0 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .export-container {
                padding: 15px;
            }
            .export-header {
                padding: 20px;
            }
            .header-title h1 {
                font-size: 2rem;
            }
            .export-grid {
                grid-template-columns: 1fr;
            }
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
<div class="export-container">
    <!-- En-tête -->
    <div class="export-header">
        <div class="header-title">
            <div class="title-icon">
                <i class="fas fa-download"></i>
            </div>
            <h1>Export des Données</h1>
        </div>
        <p style="color: var(--text-secondary); margin: 0;">Exportez et téléchargez les données du système dans différents formats</p>
    </div>

    <!-- Statistiques d'export -->
    <div class="export-stats">
        <div class="stat-card patients">
            <div class="stat-number">2,847</div>
            <div class="stat-label">Patients</div>
        </div>
        <div class="stat-card sensors">
            <div class="stat-number">156</div>
            <div class="stat-label">Capteurs</div>
        </div>
        <div class="stat-card logs">
            <div class="stat-number">45,230</div>
            <div class="stat-label">Logs</div>
        </div>
        <div class="stat-card reports">
            <div class="stat-number">1,264</div>
            <div class="stat-label">Rapports</div>
        </div>
    </div>

    <!-- Filtres d'export -->
    <div class="export-filters">
        <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
            <i class="fas fa-filter" style="margin-right: 10px; color: #667eea;"></i>
            Filtres d'Export
        </h3>
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Date de début</label>
                <input type="date" class="filter-input" id="startDate">
            </div>
            <div class="filter-group">
                <label class="filter-label">Date de fin</label>
                <input type="date" class="filter-input" id="endDate">
            </div>
            <div class="filter-group">
                <label class="filter-label">Service</label>
                <select class="filter-select" id="serviceFilter">
                    <option value="">Tous les services</option>
                    <option value="cardiology">Cardiologie</option>
                    <option value="pneumology">Pneumologie</option>
                    <option value="emergency">Urgences</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Statut</label>
                <select class="filter-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="critical">Critique</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grille d'export -->
    <div class="export-grid">
        <!-- Options d'export de données -->
        <div class="export-section">
            <div class="section-header">
                <i class="fas fa-database" style="color: #667eea;"></i>
                <h3 class="section-title">Données Principales</h3>
            </div>
            
            <div class="export-option">
                <div class="option-info">
                    <div class="option-icon patients">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="option-details">
                        <h4>Données Patients</h4>
                        <p>Informations complètes des patients, historique médical</p>
                        <span class="data-count">2,847 enregistrements</span>
                        <div class="format-selector">
                            <button class="format-btn active" data-format="csv">CSV</button>
                            <button class="format-btn" data-format="excel">Excel</button>
                            <button class="format-btn" data-format="json">JSON</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="exportData('patients')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>

            <div class="export-option">
                <div class="option-info">
                    <div class="option-icon sensors">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="option-details">
                        <h4>Données Capteurs</h4>
                        <p>Relevés des capteurs, configurations, historique</p>
                        <span class="data-count">156 capteurs, 89,432 mesures</span>
                        <div class="format-selector">
                            <button class="format-btn active" data-format="csv">CSV</button>
                            <button class="format-btn" data-format="excel">Excel</button>
                            <button class="format-btn" data-format="json">JSON</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-success" onclick="exportData('sensors')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>

            <div class="export-option">
                <div class="option-info">
                    <div class="option-icon alerts">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="option-details">
                        <h4>Alertes Système</h4>
                        <p>Historique des alertes, notifications, incidents</p>
                        <span class="data-count">3,421 alertes</span>
                        <div class="format-selector">
                            <button class="format-btn active" data-format="csv">CSV</button>
                            <button class="format-btn" data-format="excel">Excel</button>
                            <button class="format-btn" data-format="pdf">PDF</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-warning" onclick="exportData('alerts')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
        </div>

        <!-- Options d'export système -->
        <div class="export-section">
            <div class="section-header">
                <i class="fas fa-cogs" style="color: #667eea;"></i>
                <h3 class="section-title">Données Système</h3>
            </div>
            
            <div class="export-option">
                <div class="option-info">
                    <div class="option-icon reports">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="option-details">
                        <h4>Rapports Médicaux</h4>
                        <p>Rapports générés, analyses, prescriptions</p>
                        <span class="data-count">1,264 rapports</span>
                        <div class="format-selector">
                            <button class="format-btn active" data-format="pdf">PDF</button>
                            <button class="format-btn" data-format="excel">Excel</button>
                            <button class="format-btn" data-format="zip">ZIP</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-danger" onclick="exportData('reports')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>

            <div class="export-option">
                <div class="option-info">
                    <div class="option-icon logs">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="option-details">
                        <h4>Logs Système</h4>
                        <p>Journaux d'activité, erreurs, traces d'audit</p>
                        <span class="data-count">45,230 entrées</span>
                        <div class="format-selector">
                            <button class="format-btn active" data-format="txt">TXT</button>
                            <button class="format-btn" data-format="csv">CSV</button>
                            <button class="format-btn" data-format="zip">ZIP</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="exportData('logs')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>

            <div class="export-option">
                <div class="option-info">
                    <div class="option-icon users">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="option-details">
                        <h4>Données Utilisateurs</h4>
                        <p>Comptes utilisateurs, rôles, permissions</p>
                        <span class="data-count">156 utilisateurs</span>
                        <div class="format-selector">
                            <button class="format-btn active" data-format="csv">CSV</button>
                            <button class="format-btn" data-format="excel">Excel</button>
                            <button class="format-btn" data-format="json">JSON</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-success" onclick="exportData('users')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
        </div>
    </div>

    <!-- Historique des exports -->
    <div class="export-history">
        <div class="section-header">
            <i class="fas fa-history" style="color: #667eea;"></i>
            <h3 class="section-title">Historique des Exports</h3>
        </div>
        
        <div class="history-item">
            <div class="history-info">
                <div class="history-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="history-details">
                    <h5>Export Données Patients</h5>
                    <span>Il y a 2 heures • 2,847 enregistrements • CSV • 12.4MB</span>
                </div>
            </div>
            <button class="btn btn-primary" onclick="downloadFile('patients_export_20240115.csv')">
                <i class="fas fa-download"></i>
                Télécharger
            </button>
        </div>
        
        <div class="history-item">
            <div class="history-info">
                <div class="history-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="history-details">
                    <h5>Export Données Capteurs</h5>
                    <span>Hier • 89,432 mesures • Excel • 45.8MB</span>
                </div>
            </div>
            <button class="btn btn-primary" onclick="downloadFile('sensors_export_20240114.xlsx')">
                <i class="fas fa-download"></i>
                Télécharger
            </button>
        </div>
        
        <div class="history-item">
            <div class="history-info">
                <div class="history-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="history-details">
                    <h5>Export Rapports Médicaux</h5>
                    <span>Il y a 3 jours • 1,264 rapports • PDF • 234.2MB</span>
                </div>
            </div>
            <button class="btn btn-primary" onclick="downloadFile('reports_export_20240112.zip')">
                <i class="fas fa-download"></i>
                Télécharger
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de format
    document.querySelectorAll('.format-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.format-selector');
            container.querySelectorAll('.format-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Animation d'entrée
    const cards = document.querySelectorAll('.stat-card, .export-section, .export-history');
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

function exportData(type) {
    const typeNames = {
        patients: 'Patients',
        sensors: 'Capteurs',
        alerts: 'Alertes',
        reports: 'Rapports',
        logs: 'Logs',
        users: 'Utilisateurs'
    };
    
    showToast(`Export des données ${typeNames[type]} en cours...`, 'info');
    
    // Simulation de l'export
    setTimeout(() => {
        showToast(`Export ${typeNames[type]} terminé avec succès`, 'success');
    }, Math.random() * 3000 + 2000);
}

function downloadFile(filename) {
    showToast(`Téléchargement de ${filename}...`, 'info');
    
    // Simulation du téléchargement
    setTimeout(() => {
        showToast('Téléchargement terminé', 'success');
    }, 1500);
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
</script>
@endsection