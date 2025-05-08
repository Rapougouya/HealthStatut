 // Initialisation des sélecteurs
 document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#patientFilter', {
        placeholder: 'Filtrer par patient...',
        allowEmptyOption: true
    });
    
    new TomSelect('#categoryFilter', {
        placeholder: 'Filtrer par catégorie...',
        allowEmptyOption: true
    });
    
    new TomSelect('#statusFilter', {
        placeholder: 'Filtrer par statut...',
        allowEmptyOption: true
    });
    
    // Gestion du modal
    const modal = document.getElementById('prescriptionModal');
    const btn = document.getElementById('openModalBtn');
    
    btn.onclick = function() {
        document.getElementById('modalTitle').textContent = 'Nouvelle Prescription';
        document.getElementById('prescriptionForm').reset();
        document.getElementById('prescriptionId').value = '';
        modal.style.display = 'block';
    }
    
    // Fermeture du modal en cliquant en dehors
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
        
        const confirmModal = document.getElementById('confirmModal');
        if (event.target == confirmModal) {
            confirmModal.style.display = 'none';
        }
    }
});

// Fonctions pour gérer les prescriptions
function editPrescription(id) {
    fetch(`/prescriptions/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Modifier Prescription';
            document.getElementById('prescriptionId').value = data.id;
            document.getElementById('patient_id').value = data.patient_id;
            document.getElementById('medecin').value = data.medecin;
            document.getElementById('categorie').value = data.categorie;
            document.getElementById('statut').value = data.statut;
            document.getElementById('medicament').value = data.medicament;
            document.getElementById('posologie').value = data.posologie;
            document.getElementById('notes').value = data.notes || '';
            document.getElementById('date_debut').value = data.date_debut;
            document.getElementById('date_fin').value = data.date_fin;
            
            document.getElementById('prescriptionModal').style.display = 'block';
        });
}

function closeModal() {
    document.getElementById('prescriptionModal').style.display = 'none';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

function confirmDelete(id) {
    const modal = document.getElementById('confirmModal');
    document.getElementById('confirmDeleteBtn').onclick = function() {
        deletePrescription(id);
        modal.style.display = 'none';
    };
    modal.style.display = 'block';
}

function deletePrescription(id) {
    fetch(`/prescriptions/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        }
    });
}

function printPrescription(id) {
    window.open(`/prescriptions/${id}/print`, '_blank');
}

// Gestion du formulaire
document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = document.getElementById('prescriptionId').value;
    const url = id ? `/prescriptions/${id}` : '/prescriptions';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        }
    });
});

// Filtrage des prescriptions
document.getElementById('patientFilter').addEventListener('change', filterPrescriptions);
document.getElementById('categoryFilter').addEventListener('change', filterPrescriptions);
document.getElementById('statusFilter').addEventListener('change', filterPrescriptions);
document.getElementById('dateFilter').addEventListener('change', filterPrescriptions);

function filterPrescriptions() {
    const patientId = document.getElementById('patientFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    
    let url = '/prescriptions?';
    if (patientId) url += `patient_id=${patientId}&`;
    if (category) url += `categorie=${category}&`;
    if (status) url += `statut=${status}&`;
    if (date) url += `date=${date}&`;
    
    window.location.href = url.slice(0, -1); // Supprimer le dernier '&'
}