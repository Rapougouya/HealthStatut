@extends('layouts.app')

@section('title', 'HealthStatut - Liste des Patients')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/patient_list.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="flex h-screen bg-gray-50 overflow-hidden">
    {{-- Contenu principal --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Barre de navigation supérieure --}}
        <header class="bg-white border-b border-gray-200 shadow-sm py-3 px-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800">
                    Liste des Patients
                </h1>
                <div class="flex items-center space-x-4">
                </div>
    
                {{-- Afficher les liens de pagination si paginé --}}
                @if(method_exists($patients, 'links'))
                    {{ $patients->links() }}
                @endif
            </div>
        </header>

        {{-- Contenu de la page --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Panel de détails du patient --}}
            <div id="patientDetailsPanel" class="patient-details-panel hidden">
                <div class="patient-details-header">
                    <h3>Détails du Patient</h3>
                    <button id="closeDetailsBtn" class="close-details-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="patient-details-content">
                    <div class="patient-details-grid">
                        {{-- Les détails seront injectés ici via JavaScript --}}
                    </div>
                    @foreach ($patients as $patient)
                       <div class="patient-card">
                           <!-- Ici tu as accès à $patient -->
                           <a href="{{ route('patients.show', $patient->id) }}" class="view-full-profile-btn">
                               Voir profil complet
                           </a>
                       </div>
                   @endforeach
                </div>
            </div>

            {{-- Actions groupées --}}
            <div id="bulkActionsBar" class="bulk-actions hidden">
                <div class="bulk-actions-left">
                    <label class="select-all-label">
                        <input type="checkbox" id="selectAllCheckbox" class="patient-checkbox">
                        Tout sélectionner
                    </label>
                    <span class="selected-count">0 patient(s) sélectionné(s)</span>
                </div>
                <div class="bulk-actions-right">
                    <button id="viewSelectedBtn" class="bulk-action-btn view">
                        <i class="fas fa-eye"></i>
                        Voir les détails
                    </button>
                </div>
            </div>

            {{-- Barre d'outils --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="search-container w-full sm:w-64">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Rechercher un patient..." 
                            class="search-input"
                        >
                    </div>
                    <button class="filter-btn">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                
                <a href="{{ route('patients.create') }}" class="w-full sm:w-auto px-4 py-2 bg-medical-blue hover:bg-medical-darkblue text-white rounded-md shadow-sm flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Nouveau Patient
                </a>
            </div>

            {{-- Tableau des patients --}}
            <div class="bg-white rounded-md shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="patient-table">
                        <thead>
                            <tr>
                                <th class="w-[40px]">
                                    <input type="checkbox" class="patient-checkbox" id="headerCheckbox">
                                </th>
                                <th class="w-[50px]">ID</th>
                                <th class="w-[250px]">Nom Complet</th>
                                <th>Adresse</th>
                                <th>Téléphone</th>
                                <th class="w-[150px]">Date de naissance</th>
                                <th class="w-[100px]">Sexe</th>
                                <th class="w-[150px] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            @forelse($patients as $patient)
                                <tr data-patient-id="{{ $patient->id }}" class="patient-row">
                                    <td>
                                        <input type="checkbox" class="patient-checkbox patient-select" data-patient-id="{{ $patient->id }}">
                                    </td>
                                    <td>{{ $patient->id }}</td>
                                    <td>
                                        <div class="patient-info">
                                            <div class="patient-avatar">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                            <div>
                                                {{ $patient->prenom }} {{ $patient->nom }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $patient->adresse }}</td>
                                    <td>{{ $patient->telephone }}</td>
                                    <td>{{ $patient->date_naissance->format('d/m/Y') }}</td>
                                    <td>{{ $patient->sexe === 'M' ? 'Masculin' : 'Féminin' }}</td>
                                    <td>
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('patients.show', $patient->id) }}" class="action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('patients.edit', $patient->id) }}" class="action-btn">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button 
                                                class="action-btn text-red-500" 
                                                onclick="confirmDelete({{ $patient->id }})"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500">
                                        Aucun patient trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $patients->links('pagination.custun') }}
                </div>
            </div>
        </main>
    </div>

    {{-- Modal de confirmation de suppression --}}
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible.</p>
            <div class="flex justify-end space-x-3">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                    Annuler
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Variables globales
    let selectedPatients = new Set();
    let patientData = {};
    
    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // Stocker les données des patients pour un accès rapide
        @foreach($patients as $patient)
            patientData[{{ $patient->id }}] = {
                id: {{ $patient->id }},
                nom: "{{ $patient->nom }}",
                prenom: "{{ $patient->prenom }}",
                adresse: "{{ $patient->adresse }}",
                telephone: "{{ $patient->telephone }}",
                date_naissance: "{{ $patient->date_naissance->format('d/m/Y') }}",
                sexe: "{{ $patient->sexe === 'M' ? 'Masculin' : 'Féminin' }}",
                email: "{{ $patient->email ?? 'Non renseigné' }}"
            };
        @endforeach
        
        // Gestionnaires d'événements
        setupCheckboxHandlers();
        setupRowClickHandlers();
        setupBulkActionHandlers();
        setupSearchHandler();
        setupDeleteHandler();
        setupFullProfileLink();
    });
    
    // Recherche de patients
    function setupSearchHandler() {
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchQuery = this.value.toLowerCase();
            const rows = document.querySelectorAll('#patientsTableBody tr.patient-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchQuery) ? '' : 'none';
            });
        });
    }
    
    // Gestion des checkbox
    function setupCheckboxHandlers() {
        // Checkbox de l'en-tête
        const headerCheckbox = document.getElementById('headerCheckbox');
        headerCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.patient-select');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                const patientId = parseInt(checkbox.dataset.patientId);
                
                if (this.checked) {
                    selectedPatients.add(patientId);
                } else {
                    selectedPatients.delete(patientId);
                }
                
                // Mettre à jour la classe de la ligne
                const row = document.querySelector(`tr[data-patient-id="${patientId}"]`);
                if (row) {
                    if (this.checked) {
                        row.classList.add('selected');
                    } else {
                        row.classList.remove('selected');
                    }
                }
            });
            
            updateBulkActionBar();
        });
        
        // Checkbox individuels
        const patientCheckboxes = document.querySelectorAll('.patient-select');
        patientCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const patientId = parseInt(this.dataset.patientId);
                
                if (this.checked) {
                    selectedPatients.add(patientId);
                } else {
                    selectedPatients.delete(patientId);
                }
                
                // Mettre à jour la classe de la ligne
                const row = document.querySelector(`tr[data-patient-id="${patientId}"]`);
                if (row) {
                    if (this.checked) {
                        row.classList.add('selected');
                    } else {
                        row.classList.remove('selected');
                    }
                }
                
                // Vérifier si tous les checkboxes sont cochés
                const allCheckboxes = document.querySelectorAll('.patient-select');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                document.getElementById('headerCheckbox').checked = allChecked;
                
                // Mettre à jour la barre d'actions groupées
                updateBulkActionBar();
            });
        });
    }
    
    // Gestion des clics sur les lignes
    function setupRowClickHandlers() {
        const patientRows = document.querySelectorAll('.patient-row');
        patientRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Ne pas déclencher si le clic est sur un bouton d'action ou une checkbox
                if (e.target.closest('.action-btn') || e.target.closest('.patient-checkbox')) {
                    return;
                }
                
                const patientId = parseInt(this.dataset.patientId);
                const checkbox = this.querySelector('.patient-checkbox');
                
                checkbox.checked = !checkbox.checked;
                
                // Déclencher l'événement change pour activer les gestionnaires d'événements
                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });
        });
    }
    
    // Gestion des actions groupées
    function setupBulkActionHandlers() {
        // Bouton pour voir les détails
        document.getElementById('viewSelectedBtn').addEventListener('click', function() {
            if (selectedPatients.size === 1) {
                const patientId = Array.from(selectedPatients)[0];
                showPatientDetails(patientId);
            }
        });
        
        // Bouton pour fermer les détails
        document.getElementById('closeDetailsBtn').addEventListener('click', function() {
            hidePatientDetails();
        });
        
        // Checkbox de sélection globale
        document.getElementById('selectAllCheckbox').addEventListener('change', function() {
            document.getElementById('headerCheckbox').checked = this.checked;
            const event = new Event('change');
            document.getElementById('headerCheckbox').dispatchEvent(event);
        });
    }
    
    // Configuration du lien vers le profil complet
    function setupFullProfileLink() {
        const fullProfileBtn = document.getElementById('viewFullProfileBtn');
        if (fullProfileBtn) {
            fullProfileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const patientId = this.getAttribute('data-patient-id');
                if (patientId) {
                    window.location.href = `/patients/${patientId}`;
                }
            });
        }
    }
    
    // Mise à jour de la barre d'actions groupées
    function updateBulkActionBar() {
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.querySelector('.selected-count');
        const viewSelectedBtn = document.getElementById('viewSelectedBtn');
        
        if (selectedPatients.size > 0) {
            bulkActionsBar.classList.remove('hidden');
            selectedCount.textContent = `${selectedPatients.size} patient(s) sélectionné(s)`;
            
            // Activer le bouton de détails uniquement si un seul patient est sélectionné
            if (selectedPatients.size === 1) {
                viewSelectedBtn.disabled = false;
                viewSelectedBtn.classList.remove('opacity-50');
            } else {
                viewSelectedBtn.disabled = true;
                viewSelectedBtn.classList.add('opacity-50');
            }
        } else {
            bulkActionsBar.classList.add('hidden');
        }
    }
    
    // Afficher les détails d'un patient
    function showPatientDetails(patientId) {
        const patient = patientData[patientId];
        if (!patient) return;
        
        const detailsPanel = document.getElementById('patientDetailsPanel');
        const detailsContent = detailsPanel.querySelector('.patient-details-grid');
        const fullProfileBtn = document.getElementById('viewFullProfileBtn');
        
        // Configurer le lien vers le profil complet
        if (fullProfileBtn) {
            fullProfileBtn.setAttribute('data-patient-id', patient.id);
        }
        
        // Remplir les détails avec des styles améliorés
        detailsContent.innerHTML = `
            <div class="patient-detail-header">
                <div class="patient-avatar-large">
                    <i class="fas fa-user-circle fa-3x"></i>
                </div>
                <div class="patient-name-large">
                    ${patient.prenom} ${patient.nom}
                </div>
            </div>

            <x-patient.detail label="ID" value="${patient.id}" />
            <x-patient.detail label="Nom" value="${patient.nom}" />
            <x-patient.detail label="Prénom" value="${patient.prenom}" />
            <x-patient.detail label="Adresse" value="${patient.adresse}" />
            <x-patient.detail label="Téléphone" value="${patient.telephone}" />
            <x-patient.detail label="Date de naissance" value="${patient.date_naissance}" />
            <x-patient.detail label="Sexe" value="${patient.sexe}" />
            <x-patient.detail label="Email" value="${patient.email}" />
        `;
        
        // Afficher le panel
        detailsPanel.classList.remove('hidden');
        detailsPanel.classList.add('slide-in');
        
        // Faire défiler vers le haut de la page
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Cacher les détails d'un patient
    function hidePatientDetails() {
        const detailsPanel = document.getElementById('patientDetailsPanel');
        detailsPanel.classList.add('slide-out');
        
        // Attendre la fin de l'animation avant de cacher le panel
        setTimeout(() => {
            detailsPanel.classList.remove('slide-in', 'slide-out');
            detailsPanel.classList.add('hidden');
        }, 300);
    }
    
    // Confirmation de suppression
    function setupDeleteHandler() {
        // Gestionnaires déjà existants
        const cancelButton = document.getElementById('cancelDelete');
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                const modal = document.getElementById('deleteModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }
    }
    
    function confirmDelete(patientId) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        
        form.action = `/patients/${patientId}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
</script>
@endsection