@extends('layouts.app')

@section('title', 'Gestion des Prescriptions')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

@endsection

@section('content')
<div class="prescriptions-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-prescription-bottle-alt mr-2"></i>Gestion des Prescriptions
        </h1>
        <button id="openModalBtn" class="btn-add">
            <i class="fas fa-plus mr-2"></i>Nouvelle Prescription
        </button>
    </div>

    <div class="card">
        <div class="filter-form">
            <div class="form-group">
                <label for="patientFilter" class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                <select id="patientFilter" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Tous les patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->nom_complet }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                <select id="categoryFilter" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Toutes catégories</option>
                    <option value="Antibiotique">Antibiotique</option>
                    <option value="Antidouleur">Antidouleur</option>
                    <option value="Antihistaminique">Antihistaminique</option>
                </select>
            </div>
            <div class="form-group">
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Tous statuts</option>
                    <option value="active">Active</option>
                    <option value="completed">Terminée</option>
                    <option value="cancelled">Annulée</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="dateFilter" class="w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>

        <div class="table-responsive">
            <table class="prescriptions-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Médicament</th>
                        <th>Posologie</th>
                        <th>Catégorie</th>
                        <th>Dates</th>
                        <th>Statut</th>
                        <th>Médecin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $prescription)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" 
                                         src="{{ $prescription->patient->avatar_url }}" 
                                         alt="{{ $prescription->patient->nom_complet }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $prescription->patient->nom_complet }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ID: {{ $prescription->patient->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="font-medium">{{ $prescription->medicament }}</td>
                        <td>{{ $prescription->posologie }}</td>
                        <td>{{ $prescription->categorie }}</td>
                        <td>
                            <div class="text-sm">
                                <div>Début: {{ $prescription->date_debut->format('d/m/Y') }}</div>
                                <div>Fin: {{ $prescription->date_fin->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <td>
                            @if($prescription->statut == 'active')
                                <span class="badge badge-active">
                                    <i class="fas fa-circle-notch fa-spin mr-1"></i> Active
                                </span>
                            @elseif($prescription->statut == 'completed')
                                <span class="badge badge-completed">
                                    <i class="fas fa-check-circle mr-1"></i> Terminée
                                </span>
                            @else
                                <span class="badge badge-cancelled">
                                    <i class="fas fa-times-circle mr-1"></i> Annulée
                                </span>
                            @endif
                        </td>
                        <td>{{ $prescription->medecin }}</td>
                        <td>
                            <div class="flex space-x-2">
                                <button class="action-btn edit" title="Modifier" onclick="editPrescription({{ $prescription->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete" title="Supprimer" onclick="confirmDelete({{ $prescription->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <button class="action-btn" title="Imprimer" onclick="printPrescription({{ $prescription->id }})">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center text-gray-500">
                            Aucune prescription trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $prescriptions->links() }}
        </div>
    </div>
</div>

<!-- Modal pour ajouter/modifier une prescription -->
<div id="prescriptionModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2 class="text-xl font-semibold mb-4" id="modalTitle">Nouvelle Prescription</h2>
        <form id="prescriptionForm">
            @csrf
            <input type="hidden" id="prescriptionId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-group">
                    <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                    <select name="patient_id" id="patient_id" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Sélectionner un patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->nom_complet }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="medecin" class="block text-sm font-medium text-gray-700">Médecin</label>
                    <input type="text" name="medecin" id="medecin" class="w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="form-group">
                    <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie" id="categorie" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Sélectionner une catégorie</option>
                        <option value="Antibiotique">Antibiotique</option>
                        <option value="Antidouleur">Antidouleur</option>
                        <option value="Antihistaminique">Antihistaminique</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="statut" id="statut" class="w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="active">Active</option>
                        <option value="completed">Terminée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="medicament" class="block text-sm font-medium text-gray-700">Médicament</label>
                    <input type="text" name="medicament" id="medicament" class="w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-group">
                    <label for="posologie" class="block text-sm font-medium text-gray-700">Posologie</label>
                    <textarea name="posologie" id="posologie" rows="2" class="w-full rounded-md border-gray-300 shadow-sm" required></textarea>
                </div>
                <div class="form-group">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-group">
                    <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" class="w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="form-group">
                    <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmation -->
<div id="confirmModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="text-center p-6">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-sm text-gray-500 mb-6">Êtes-vous sûr de vouloir supprimer cette prescription? Cette action est irréversible.</p>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="button" id="confirmDeleteBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
@endsection