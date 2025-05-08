@extends('layouts.app')

@section('title', 'Paramètres des Alertes')

@section('content')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<style>
    .form-header i {
        color: #dc3545;
        background: #ffe5e9;
        border-radius: 50%;
        padding: 0.25em 0.4em;
    }
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .settings-card {
        background: #fff;
        border-radius: 0.5rem;
        padding: 1.25rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }
    .settings-card h4 {
        display: flex;
        align-items: center;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: #333;
    }
    .settings-card h4 i {
        margin-right: 0.5rem;
        color: #dc3545;
        font-size: 1rem;
    }
    .threshold-settings {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .threshold-group, .severity-group {
        display: flex;
        flex-direction: column;
    }
    .threshold-group label, .severity-group label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        color: #555;
    }
    .notification-settings {
        margin-top: 2rem;
    }
    .notification-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .notification-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .notification-option input[type="checkbox"] {
        width: 1.1rem;
        height: 1.1rem;
    }
    .settings-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }
    .reset-btn {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .save-btn {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 0.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        .notification-options {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container mt-4">
    <div class="form-header">
        <h1>
            <i class="fas fa-bell icon-title"></i>
            Paramètres des Alertes
        </h1>
        <p class="text-muted">Configurez les seuils d'alerte et les préférences de notification</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('alertes.settings.save') }}" method="POST" class="mt-4">
        @csrf

        <div class="settings-grid">
            <!-- Fréquence cardiaque -->
            <div class="settings-card">
                <h4><i class="fas fa-heartbeat"></i> Rythme Cardiaque</h4>
                <div class="threshold-settings">
                    <div class="threshold-group">
                        <label for="heart_rate_low">Seuil minimum (bpm)</label>
                        <input type="number" name="heart_rate_low" id="heart_rate_low" 
                               value="{{ $settings->heart_rate_low ?? 60 }}" 
                               min="40" max="100" class="form-control">
                    </div>
                    <div class="threshold-group">
                        <label for="heart_rate_high">Seuil maximum (bpm)</label>
                        <input type="number" name="heart_rate_high" id="heart_rate_high" 
                               value="{{ $settings->heart_rate_high ?? 100 }}" 
                               min="60" max="180" class="form-control">
                    </div>
                    <div class="severity-group">
                        <label for="heart_rate_severity">Sévérité</label>
                        <select name="heart_rate_severity" id="heart_rate_severity" class="form-control">
                            <option value="low" {{ ($settings->heart_rate_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="medium" {{ ($settings->heart_rate_severity ?? '') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ ($settings->heart_rate_severity ?? 'high') === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="critical" {{ ($settings->heart_rate_severity ?? '') === 'critical' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Saturation en oxygène -->
            <div class="settings-card">
                <h4><i class="fas fa-lungs"></i> Saturation en Oxygène</h4>
                <div class="threshold-settings">
                    <div class="threshold-group">
                        <label for="spo2_low">Seuil minimum (%)</label>
                        <input type="number" name="spo2_low" id="spo2_low" 
                               value="{{ $settings->spo2_low ?? 94 }}" 
                               min="80" max="100" class="form-control">
                    </div>
                    <div class="severity-group">
                        <label for="spo2_severity">Sévérité</label>
                        <select name="spo2_severity" id="spo2_severity" class="form-control">
                            <option value="low" {{ ($settings->spo2_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="medium" {{ ($settings->spo2_severity ?? '') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ ($settings->spo2_severity ?? '') === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="critical" {{ ($settings->spo2_severity ?? 'critical') === 'critical' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Température -->
            <div class="settings-card">
                <h4><i class="fas fa-thermometer-half"></i> Température</h4>
                <div class="threshold-settings">
                    <div class="threshold-group">
                        <label for="temp_low">Seuil minimum (°C)</label>
                        <input type="number" name="temp_low" id="temp_low" 
                               value="{{ $settings->temp_low ?? 36.0 }}" 
                               min="34.0" max="37.0" step="0.1" class="form-control">
                    </div>
                    <div class="threshold-group">
                        <label for="temp_high">Seuil maximum (°C)</label>
                        <input type="number" name="temp_high" id="temp_high" 
                               value="{{ $settings->temp_high ?? 38.0 }}" 
                               min="37.0" max="42.0" step="0.1" class="form-control">
                    </div>
                    <div class="severity-group">
                        <label for="temp_severity">Sévérité</label>
                        <select name="temp_severity" id="temp_severity" class="form-control">
                            <option value="low" {{ ($settings->temp_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="medium" {{ ($settings->temp_severity ?? '') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ ($settings->temp_severity ?? 'high') === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="critical" {{ ($settings->temp_severity ?? '') === 'critical' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pression artérielle -->
            <div class="settings-card">
                <h4><i class="fas fa-tachometer-alt"></i> Pression Artérielle</h4>
                <div class="threshold-settings">
                    <div class="threshold-group">
                        <label for="bp_sys_low">Systolique min (mmHg)</label>
                        <input type="number" name="bp_sys_low" id="bp_sys_low" 
                               value="{{ $settings->bp_sys_low ?? 90 }}" 
                               min="70" max="120" class="form-control">
                    </div>
                    <div class="threshold-group">
                        <label for="bp_sys_high">Systolique max (mmHg)</label>
                        <input type="number" name="bp_sys_high" id="bp_sys_high" 
                               value="{{ $settings->bp_sys_high ?? 140 }}" 
                               min="120" max="200" class="form-control">
                    </div>
                    <div class="threshold-group">
                        <label for="bp_dia_low">Diastolique min (mmHg)</label>
                        <input type="number" name="bp_dia_low" id="bp_dia_low" 
                               value="{{ $settings->bp_dia_low ?? 60 }}" 
                               min="40" max="80" class="form-control">
                    </div>
                    <div class="threshold-group">
                        <label for="bp_dia_high">Diastolique max (mmHg)</label>
                        <input type="number" name="bp_dia_high" id="bp_dia_high" 
                               value="{{ $settings->bp_dia_high ?? 90 }}" 
                               min="80" max="120" class="form-control">
                    </div>
                    <div class="severity-group">
                        <label for="bp_severity">Sévérité</label>
                        <select name="bp_severity" id="bp_severity" class="form-control">
                            <option value="low" {{ ($settings->bp_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="medium" {{ ($settings->bp_severity ?? 'medium') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ ($settings->bp_severity ?? '') === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="critical" {{ ($settings->bp_severity ?? '') === 'critical' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="notification-settings">
            <h3><i class="fas fa-bell"></i> Préférences de notification</h3>
            <div class="notification-options">
                <div class="notification-option">
                    <input type="checkbox" name="notify_email" id="notify_email" {{ ($settings->notify_email ?? true) ? 'checked' : '' }}>
                    <label for="notify_email">Recevoir les notifications par email</label>
                </div>
                <div class="notification-option">
                    <input type="checkbox" name="notify_sms" id="notify_sms" {{ ($settings->notify_sms ?? true) ? 'checked' : '' }}>
                    <label for="notify_sms">Recevoir les notifications par SMS</label>
                </div>
                <div class="notification-option">
                    <input type="checkbox" name="notify_app" id="notify_app" {{ ($settings->notify_app ?? true) ? 'checked' : '' }}>
                    <label for="notify_app">Recevoir les notifications dans l'application</label>
                </div>
                <div class="notification-option">
                    <input type="checkbox" name="notify_critical_only" id="notify_critical_only" {{ ($settings->notify_critical_only ?? false) ? 'checked' : '' }}>
                    <label for="notify_critical_only">Notifier uniquement les alertes critiques</label>
                </div>
            </div>
        </div>

        <div class="settings-actions">
            <button type="button" id="reset-settings" class="reset-btn" data-url="{{ route('alertes.settings.reset') }}">
                <i class="fas fa-undo"></i> Réinitialiser les paramètres
            </button>
            <button type="submit" class="save-btn">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script src="https://kit.fontawesome.com/5bf5cbe1cf.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gérer la réinitialisation des paramètres
        const resetBtn = document.getElementById('reset-settings');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres d\'alerte aux valeurs par défaut ?')) {
                    const resetUrl = this.getAttribute('data-url');
                    
                    fetch(resetUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Une erreur est survenue lors de la réinitialisation des paramètres.');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de la réinitialisation des paramètres.');
                    });
                }
            });
        }
    });
</script>
@endsection