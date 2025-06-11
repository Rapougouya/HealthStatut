@extends('layouts.app')

@section('title', 'Logs Système')

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

        .logs-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .logs-header {
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

        .logs-stats {
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

        .stat-card.errors { border-left-color: var(--danger-color); }
        .stat-card.warnings { border-left-color: var(--warning-color); }
        .stat-card.info { border-left-color: var(--info-color); }
        .stat-card.success { border-left-color: var(--success-color); }

        .logs-content {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .logs-filters {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-select, .filter-input {
            padding: 10px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            cursor: pointer;
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

        .logs-table-container {
            max-height: 600px;
            overflow-y: auto;
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logs-table th {
            background: rgba(102, 126, 234, 0.1);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .logs-table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
            vertical-align: top;
        }

        .logs-table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .log-level {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .log-level.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .log-level.warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .log-level.info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }

        .log-level.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .log-message {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: pointer;
        }

        .log-message:hover {
            white-space: normal;
            max-width: none;
        }

        .log-timestamp {
            font-family: monospace;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .log-source {
            font-size: 0.85rem;
            color: var(--text-secondary);
            background: rgba(102, 126, 234, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
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

        .log-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .logs-container {
                padding: 15px;
            }
            .logs-header {
                padding: 20px;
            }
            .header-title h1 {
                font-size: 2rem;
            }
            .logs-filters {
                flex-direction: column;
                align-items: stretch;
            }
            .logs-table {
                font-size: 0.8rem;
            }
            .logs-table th,
            .logs-table td {
                padding: 8px;
            }
        }
    </style>
@endsection

@section('content')
<div class="logs-container">
    <!-- En-tête -->
    <div class="logs-header">
        <div class="header-title">
            <div class="title-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h1>Logs Système</h1>
        </div>
        <p style="color: var(--text-secondary); margin: 0;">Surveillez et analysez les journaux d'activité du système</p>
    </div>

    <!-- Statistiques des logs -->
    <div class="logs-stats">
        <div class="stat-card errors">
            <div class="stat-number">23</div>
            <div class="stat-label">Erreurs</div>
        </div>
        <div class="stat-card warnings">
            <div class="stat-number">156</div>
            <div class="stat-label">Avertissements</div>
        </div>
        <div class="stat-card info">
            <div class="stat-number">1,247</div>
            <div class="stat-label">Informations</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number">8,934</div>
            <div class="stat-label">Succès</div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="logs-content">
        <!-- Actions rapides -->
        <div class="log-actions">
            <strong>Actions rapides :</strong>
            <button class="btn btn-success" onclick="refreshLogs()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
            <button class="btn btn-warning" onclick="downloadLogs()">
                <i class="fas fa-download"></i>
                Télécharger
            </button>
            <button class="btn btn-danger" onclick="clearLogs()">
                <i class="fas fa-trash"></i>
                Vider
            </button>
        </div>

        <!-- Filtres -->
        <div class="logs-filters">
            <div class="filter-group">
                <label><strong>Niveau :</strong></label>
                <select class="filter-select" id="levelFilter">
                    <option value="">Tous</option>
                    <option value="error">Erreurs</option>
                    <option value="warning">Avertissements</option>
                    <option value="info">Informations</option>
                    <option value="success">Succès</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label><strong>Source :</strong></label>
                <select class="filter-select" id="sourceFilter">
                    <option value="">Toutes</option>
                    <option value="auth">Authentification</option>
                    <option value="database">Base de données</option>
                    <option value="mail">Email</option>
                    <option value="sensor">Capteurs</option>
                    <option value="system">Système</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label><strong>Date :</strong></label>
                <input type="date" class="filter-input" id="dateFilter">
            </div>
            
            <div class="filter-group">
                <label><strong>Recherche :</strong></label>
                <input type="text" class="filter-input" placeholder="Rechercher..." id="searchFilter">
            </div>
        </div>

        <!-- Tableau des logs -->
        <div class="logs-table-container">
            <table class="logs-table" id="logsTable">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Niveau</th>
                        <th>Source</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-level="error" data-source="auth" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:32:15</td>
                        <td><span class="log-level error">Error</span></td>
                        <td><span class="log-source">Auth</span></td>
                        <td class="log-message">Tentative de connexion échouée pour l'utilisateur admin@healthstatut.com</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(1)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr data-level="warning" data-source="database" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:30:42</td>
                        <td><span class="log-level warning">Warning</span></td>
                        <td><span class="log-source">Database</span></td>
                        <td class="log-message">Connexion à la base de données lente: 2.5s</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(2)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr data-level="info" data-source="sensor" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:28:33</td>
                        <td><span class="log-level info">Info</span></td>
                        <td><span class="log-source">Sensor</span></td>
                        <td class="log-message">Nouveau capteur connecté: TEMP_001 (Patient: Jean Dupont)</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(3)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr data-level="success" data-source="mail" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:25:17</td>
                        <td><span class="log-level success">Success</span></td>
                        <td><span class="log-source">Mail</span></td>
                        <td class="log-message">Email de notification envoyé avec succès à dr.martin@healthstatut.com</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(4)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr data-level="error" data-source="system" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:20:55</td>
                        <td><span class="log-level error">Error</span></td>
                        <td><span class="log-source">System</span></td>
                        <td class="log-message">Erreur lors de la sauvegarde automatique: Espace disque insuffisant</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(5)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr data-level="info" data-source="auth" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:18:22</td>
                        <td><span class="log-level info">Info</span></td>
                        <td><span class="log-source">Auth</span></td>
                        <td class="log-message">Utilisateur connecté: Dr. Sophie Martin (IP: 192.168.1.45)</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(6)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <tr data-level="warning" data-source="sensor" data-date="2024-01-15">
                        <td class="log-timestamp">2024-01-15 14:15:08</td>
                        <td><span class="log-level warning">Warning</span></td>
                        <td><span class="log-source">Sensor</span></td>
                        <td class="log-message">Capteur HEART_003 signal faible (Patient: Marie Dubois)</td>
                        <td>
                            <button class="btn btn-primary" onclick="viewLogDetails(7)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtres
    const levelFilter = document.getElementById('levelFilter');
    const sourceFilter = document.getElementById('sourceFilter');
    const dateFilter = document.getElementById('dateFilter');
    const searchFilter = document.getElementById('searchFilter');
    
    function filterLogs() {
        const rows = document.querySelectorAll('#logsTable tbody tr');
        const levelValue = levelFilter.value;
        const sourceValue = sourceFilter.value;
        const dateValue = dateFilter.value;
        const searchValue = searchFilter.value.toLowerCase();
        
        rows.forEach(row => {
            const level = row.getAttribute('data-level');
            const source = row.getAttribute('data-source');
            const date = row.getAttribute('data-date');
            const message = row.querySelector('.log-message').textContent.toLowerCase();
            
            const matchesLevel = !levelValue || level === levelValue;
            const matchesSource = !sourceValue || source === sourceValue;
            const matchesDate = !dateValue || date === dateValue;
            const matchesSearch = !searchValue || message.includes(searchValue);
            
            if (matchesLevel && matchesSource && matchesDate && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    levelFilter.addEventListener('change', filterLogs);
    sourceFilter.addEventListener('change', filterLogs);
    dateFilter.addEventListener('change', filterLogs);
    searchFilter.addEventListener('input', filterLogs);
    
    // Animation d'entrée
    const cards = document.querySelectorAll('.stat-card, .logs-content');
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

function refreshLogs() {
    showToast('Actualisation des logs...', 'info');
    setTimeout(() => {
        showToast('Logs actualisés avec succès', 'success');
    }, 1500);
}

function downloadLogs() {
    showToast('Téléchargement des logs en cours...', 'info');
    // Simuler le téléchargement
    setTimeout(() => {
        showToast('Logs téléchargés avec succès', 'success');
    }, 2000);
}

function clearLogs() {
    if (confirm('Êtes-vous sûr de vouloir vider tous les logs ? Cette action est irréversible.')) {
        showToast('Logs vidés avec succès', 'warning');
    }
}

function viewLogDetails(logId) {
    showToast(`Affichage des détails du log ${logId}`, 'info');
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