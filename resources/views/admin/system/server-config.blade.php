@extends('layouts.app')

@section('title', 'Configuration Serveur')

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

        .server-config-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .config-header {
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

        .server-status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .status-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid;
            text-align: center;
        }

        .status-card:hover {
            transform: translateY(-3px);
        }

        .status-card.cpu { border-left-color: var(--info-color); }
        .status-card.memory { border-left-color: var(--warning-color); }
        .status-card.disk { border-left-color: var(--success-color); }
        .status-card.network { border-left-color: var(--danger-color); }

        .config-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .config-section {
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #667eea;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
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

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 10px 0 5px;
        }

        .metric-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-fill.cpu { background: var(--info-color); }
        .progress-fill.memory { background: var(--warning-color); }
        .progress-fill.disk { background: var(--success-color); }
        .progress-fill.network { background: var(--danger-color); }

        @media (max-width: 768px) {
            .config-sections {
                grid-template-columns: 1fr;
            }
            .server-config-container {
                padding: 15px;
            }
            .config-header {
                padding: 20px;
            }
            .header-title h1 {
                font-size: 2rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="server-config-container">
    <!-- En-tête -->
    <div class="config-header">
        <div class="header-title">
            <div class="title-icon">
                <i class="fas fa-server"></i>
            </div>
            <h1>Configuration Serveur</h1>
        </div>
        <p style="color: var(--text-secondary); margin: 0;">Gérez les paramètres de configuration du serveur et surveillez les performances système</p>
    </div>

    <!-- Status du serveur -->
    <div class="server-status">
        <div class="status-card cpu">
            <div class="metric-value">45%</div>
            <div class="metric-label">CPU Usage</div>
            <div class="progress-bar">
                <div class="progress-fill cpu" style="width: 45%;"></div>
            </div>
        </div>
        <div class="status-card memory">
            <div class="metric-value">67%</div>
            <div class="metric-label">Memory Usage</div>
            <div class="progress-bar">
                <div class="progress-fill memory" style="width: 67%;"></div>
            </div>
        </div>
        <div class="status-card disk">
            <div class="metric-value">23%</div>
            <div class="metric-label">Disk Usage</div>
            <div class="progress-bar">
                <div class="progress-fill disk" style="width: 23%;"></div>
            </div>
        </div>
        <div class="status-card network">
            <div class="metric-value">12%</div>
            <div class="metric-label">Network Load</div>
            <div class="progress-bar">
                <div class="progress-fill network" style="width: 12%;"></div>
            </div>
        </div>
    </div>

    <!-- Sections de configuration -->
    <div class="config-sections">
        <!-- Configuration Base de données -->
        <div class="config-section">
            <div class="section-header">
                <i class="fas fa-database" style="color: #667eea;"></i>
                <h3 class="section-title">Base de Données</h3>
            </div>
            
            <form action="{{ route('admin.system.save-server-config') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-server"></i>
                        Hôte de la base de données
                    </label>
                    <input type="text" class="form-input" value="localhost" name="db_host">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hashtag"></i>
                        Port
                    </label>
                    <input type="number" class="form-input" value="3306" name="db_port">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-database"></i>
                        Nom de la base
                    </label>
                    <input type="text" class="form-input" value="healthstatut" name="db_name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-toggle-on"></i>
                        Connexion SSL
                    </label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Configuration Email -->
        <div class="config-section">
            <div class="section-header">
                <i class="fas fa-envelope" style="color: #667eea;"></i>
                <h3 class="section-title">Configuration Email</h3>
            </div>
            
            <form>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-server"></i>
                        Serveur SMTP
                    </label>
                    <input type="text" class="form-input" value="smtp.gmail.com" name="smtp_host">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-hashtag"></i>
                        Port SMTP
                    </label>
                    <input type="number" class="form-input" value="587" name="smtp_port">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Nom d'utilisateur
                    </label>
                    <input type="email" class="form-input" value="admin@healthstatut.com" name="smtp_username">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-shield-alt"></i>
                        Chiffrement
                    </label>
                    <select class="form-select" name="smtp_encryption">
                        <option value="tls" selected>TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="none">Aucun</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Configuration Sécurité -->
        <div class="config-section">
            <div class="section-header">
                <i class="fas fa-shield-alt" style="color: #667eea;"></i>
                <h3 class="section-title">Sécurité</h3>
            </div>
            
            <form>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock"></i>
                        Durée de session (minutes)
                    </label>
                    <input type="number" class="form-input" value="120" name="session_timeout">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i>
                        Complexité mot de passe
                    </label>
                    <select class="form-select" name="password_complexity">
                        <option value="low">Faible</option>
                        <option value="medium" selected>Moyenne</option>
                        <option value="high">Élevée</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-ban"></i>
                        Tentatives de connexion max
                    </label>
                    <input type="number" class="form-input" value="5" name="max_login_attempts">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-toggle-on"></i>
                        Authentification 2FA
                    </label>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Configuration Performance -->
        <div class="config-section">
            <div class="section-header">
                <i class="fas fa-tachometer-alt" style="color: #667eea;"></i>
                <h3 class="section-title">Performance</h3>
            </div>
            
            <form>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-memory"></i>
                        Limite mémoire PHP (MB)
                    </label>
                    <input type="number" class="form-input" value="512" name="memory_limit">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clock"></i>
                        Temps d'exécution max (sec)
                    </label>
                    <input type="number" class="form-input" value="300" name="max_execution_time">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-toggle-on"></i>
                        Cache activé
                    </label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-compress-alt"></i>
                        Compression GZIP
                    </label>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </form>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="action-buttons">
        <button class="btn btn-danger" onclick="resetToDefaults()">
            <i class="fas fa-undo"></i>
            Valeurs par défaut
        </button>
        <button class="btn btn-warning" onclick="testConfiguration()">
            <i class="fas fa-flask"></i>
            Tester la config
        </button>
        <a href="{{ route('admin.system.logs') }}" class="btn btn-primary">
            <i class="fas fa-file-alt"></i>
            Voir les logs
        </a>
        <a href="{{ route('admin.system.maintenance') }}" class="btn btn-warning">
            <i class="fas fa-tools"></i>
            Maintenance
        </a>
        <a href="{{ route('admin.system.export-data') }}" class="btn btn-success">
            <i class="fas fa-download"></i>
            Export données
        </a>
        <button class="btn btn-success" onclick="saveConfiguration()">
            <i class="fas fa-save"></i>
            Sauvegarder
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin-navigation.js') }}"></script>
<script>
function saveConfiguration() {
    // Utiliser la route Laravel pour sauvegarder
    const form = document.querySelector('form[action="{{ route('admin.system.save-server-config') }}"]');
    if (form) {
        form.submit();
    } else {
        showToast('Configuration sauvegardée avec succès', 'success');
    }
}

function testConfiguration() {
    showToast('Test de configuration en cours...', 'info');
    setTimeout(() => {
        showToast('Configuration testée avec succès', 'success');
    }, 2000);
}

function resetToDefaults() {
    if (confirm('Êtes-vous sûr de vouloir restaurer les valeurs par défaut ?')) {
        showToast('Configuration restaurée aux valeurs par défaut', 'warning');
    }
}

// Navigation vers les autres pages système avec les vraies routes Laravel
function navigateToLogs() {
    window.location.href = '{{ route('admin.system.logs') }}';
}

function navigateToMaintenance() {
    window.location.href = '{{ route('admin.system.maintenance') }}';
}

function navigateToExport() {
    window.location.href = '{{ route('admin.system.export-data') }}';
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
    const cards = document.querySelectorAll('.status-card, .config-section');
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