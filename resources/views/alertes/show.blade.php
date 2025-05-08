@extends('layouts.app')

@section('title', 'HealthMobis - Détails de l\'alerte')

@section('styles')
<link rel="stylesheet" href="{{ asset('alertes.css') }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <a href="{{ route('alerts.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Détails de l'alerte</h1>
        </div>
        
        @if(!$alert->resolved)
        <div class="flex gap-3">
            <button id="addNoteBtn" class="action-btn settings-btn">
                <i class="fas fa-sticky-note mr-2"></i>
                Ajouter note
            </button>
            <button id="resolveAlertBtn" data-alert-id="{{ $alert->id }}" class="action-btn resolve-all-btn">
                <i class="fas fa-check-circle mr-2"></i>
                Marquer comme résolu
            </button>
        </div>
        @endif
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="alert-status-banner severity-{{ $alert->severity }} mb-6 px-4 py-2 rounded-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                <span>
                    Cette alerte est actuellement 
                    <strong>{{ $alert->resolved ? 'résolue' : 'active' }}</strong>
                </span>
            </div>
        </div>

        <div class="alert-detail-grid">
            <div class="alert-detail-item">
                <div class="alert-detail-label">ID</div>
                <div class="alert-detail-value">{{ $alert->id }}</div>
            </div>
            <div class="alert-detail-item">
                <div class="alert-detail-label">Sévérité</div>
                <div class="alert-detail-value">
                    <span class="alert-severity severity-{{ $alert->severity }}">
                        {{ ucfirst($alert->severity) }}
                    </span>
                </div>
            </div>
            <div class="alert-detail-item">
                <div class="alert-detail-label">Type</div>
                <div class="alert-detail-value">{{ ucfirst($alert->type) }}</div>
            </div>
            <div class="alert-detail-item">
                <div class="alert-detail-label">Date de création</div>
                <div class="alert-detail-value">{{ $alert->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="alert-detail-item">
                <div class="alert-detail-label">Patient</div>
                <div class="alert-detail-value">
                    <a href="{{ route('patients.show', $alert->patient->id) }}" class="text-medical-blue hover:underline">
                        {{ $alert->patient->prenom }} {{ $alert->patient->nom }}
                    </a>
                </div>
            </div>
            @if($alert->resolved)
            <div class="alert-detail-item">
                <div class="alert-detail-label">Résolu par</div>
                <div class="alert-detail-value alert-detail-resolved">{{ $alert->resolvedBy->name ?? 'Système' }}</div>
            </div>
            <div class="alert-detail-item">
                <div class="alert-detail-label">Date de résolution</div>
                <div class="alert-detail-value">{{ $alert->resolved_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
        </div>

        <h4 class="font-medium text-gray-900 mb-2 mt-6">Titre</h4>
        <div class="alert-detail-message">
            <strong>{{ $alert->title }}</strong>
        </div>

        <h4 class="font-medium text-gray-900 mb-2 mt-6">Message</h4>
        <div class="alert-detail-message">
            {{ $alert->message }}
        </div>

        @if(!$alert->resolved)
        <div class="mt-8">
            <h4 class="font-medium text-gray-900 mb-3">Ajouter une note</h4>
            <form id="noteForm" action="{{ route('alerts.addNote', $alert->id) }}" method="POST">
                @csrf
                <textarea id="alert-notes" name="note" class="notes-input w-full" placeholder="Ajouter des notes sur cette alerte..."></textarea>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="action-btn settings-btn">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer la note
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if($alert->notes && count($alert->notes) > 0)
        <div class="mt-8">
            <h4 class="font-medium text-gray-900 mb-3">Notes</h4>
            <div class="space-y-4">
                @foreach($alert->notes as $note)
                <div class="bg-gray-50 p-4 rounded-md">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium">{{ $note->user->name }}</span>
                        <span class="text-sm text-gray-500">{{ $note->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-gray-700 mt-2">{{ $note->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($alert->resolved)
        <div class="alert-detail-history mt-8">
            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-history mr-2"></i> Historique
            </h4>
            <div class="history-timeline">
                <div class="history-item">
                    <div class="history-time">{{ $alert->created_at->format('d/m/Y H:i') }}</div>
                    <div class="history-text">Alerte créée pour le patient {{ $alert->patient->prenom }} {{ $alert->patient->nom }}</div>
                </div>
                @if(isset($alert->notes))
                    @foreach($alert->notes as $note)
                    <div class="history-item">
                        <div class="history-time">{{ $note->created_at->format('d/m/Y H:i') }}</div>
                        <div class="history-text">
                            Note ajoutée par {{ $note->user->name }}:
                            <div class="pl-4 italic mt-1 text-gray-600">{{ $note->content }}</div>
                        </div>
                    </div>
                    @endforeach
                @endif
                <div class="history-item">
                    <div class="history-time">{{ $alert->resolved_at->format('d/m/Y H:i') }}</div>
                    <div class="history-text">Alerte résolue par {{ $alert->resolvedBy->name ?? 'Système' }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="font-medium text-gray-900 mb-4">Informations sur le patient</h4>
        
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/3 mb-4 md:mb-0 md:pr-4">
                <div class="bg-gray-50 p-4 rounded-md h-full">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div>
                            <h5 class="font-medium">{{ $alert->patient->prenom }} {{ $alert->patient->nom }}</h5>
                            <p class="text-sm text-gray-500">ID: {{ $alert->patient->id }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm flex items-center">
                            <i class="fas fa-calendar-alt w-6 text-gray-400"></i>
                            <span>{{ $alert->patient->date_naissance ? $alert->patient->date_naissance->format('d/m/Y') : 'Non spécifié' }}</span>
                        </p>
                        <p class="text-sm flex items-center">
                            <i class="fas fa-phone w-6 text-gray-400"></i>
                            <span>{{ $alert->patient->telephone ?? 'Non spécifié' }}</span>
                        </p>
                        <p class="text-sm flex items-center">
                            <i class="fas fa-envelope w-6 text-gray-400"></i>
                            <span>{{ $alert->patient->email ?? 'Non spécifié' }}</span>
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('patients.show', $alert->patient->id) }}" class="text-medical-blue hover:underline text-sm flex items-center">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Voir profil complet
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="md:w-2/3">
                <div class="bg-gray-50 p-4 rounded-md">
                    <h5 class="font-medium mb-4">Alertes récentes pour ce patient</h5>
                    <div class="space-y-3">
                        @php
                            $recentAlerts = \App\Models\Alert::where('patient_id', $alert->patient->id)
                                ->where('id', '!=', $alert->id)
                                ->latest()
                                ->take(3)
                                ->get();
                        @endphp
                        
                        @if(count($recentAlerts) > 0)
                            @foreach($recentAlerts as $recentAlert)
                            <a href="{{ route('alerts.show', $recentAlert->id) }}" class="block bg-white p-3 rounded border border-gray-100 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="alert-severity severity-{{ $recentAlert->severity }} text-xs">
                                            {{ ucfirst($recentAlert->severity) }}
                                        </span>
                                        <h6 class="font-medium mt-1">{{ $recentAlert->title }}</h6>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $recentAlert->created_at->format('d/m/Y') }}</span>
                                </div>
                            </a>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-sm italic">Aucune autre alerte récente pour ce patient.</p>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('alerts.index', ['patient_id' => $alert->patient->id]) }}" class="text-medical-blue hover:underline text-sm flex items-center">
                            <i class="fas fa-history mr-2"></i>
                            Voir toutes les alertes de ce patient
                        </a>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md mt-4">
                    <h5 class="font-medium mb-3">Actions rapides</h5>
                    <div class="flex flex-wrap gap-2">
                        <a href="#" class="px-3 py-2 bg-white border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50 text-sm flex items-center">
                            <i class="fas fa-phone-alt mr-2 text-medical-blue"></i>
                            Contacter
                        </a>
                        <a href="#" class="px-3 py-2 bg-white border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50 text-sm flex items-center">
                            <i class="fas fa-file-medical mr-2 text-medical-blue"></i>
                            Dossier médical
                        </a>
                        <a href="#" class="px-3 py-2 bg-white border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50 text-sm flex items-center">
                            <i class="fas fa-chart-line mr-2 text-medical-blue"></i>
                            Historique vital
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de résolution -->
<div id="resolveModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="text-xl font-medium mb-4">Résoudre l'alerte</h3>
        <p class="text-gray-600 mb-6">
            Êtes-vous sûr de vouloir marquer cette alerte comme résolue ? Cette action ne peut pas être annulée.
        </p>
        <div class="flex justify-end gap-4">
            <button id="cancelResolve" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700">
                Annuler
            </button>
            <button id="confirmResolve" class="px-4 py-2 bg-green-500 hover:bg-green-600 rounded-md text-white">
                Confirmer
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resolveAlertBtn = document.getElementById('resolveAlertBtn');
        const resolveModal = document.getElementById('resolveModal');
        const cancelResolve = document.getElementById('cancelResolve');
        const confirmResolve = document.getElementById('confirmResolve');
        
        if (resolveAlertBtn) {
            resolveAlertBtn.addEventListener('click', function() {
                resolveModal.classList.remove('hidden');
            });
        }
        
        if (cancelResolve) {
            cancelResolve.addEventListener('click', function() {
                resolveModal.classList.add('hidden');
            });
        }
        
        if (confirmResolve) {
            confirmResolve.addEventListener('click', function() {
                const alertId = resolveAlertBtn.dataset.alertId;
                
                fetch(`/alerts/${alertId}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Ajouter un toast ou une notification
                        window.location.reload();
                    } else {
                        console.error('Erreur lors de la résolution de l\'alerte');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
                
                resolveModal.classList.add('hidden');
            });
        }
        
        // Mettre en évidence l'alerte actuelle dans la liste des alertes récentes
        const currentAlertId = "{{ $alert->id }}";
        const alertItems = document.querySelectorAll('.alert-item');
        alertItems.forEach(item => {
            if (item.dataset.alertId === currentAlertId) {
                item.classList.add('current-alert');
            }
        });
    });
</script>
@endsection