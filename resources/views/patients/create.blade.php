@extends('layouts.app')

@section('title', 'HealthStatut - Ajouter Patient')

@section('content')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.form-group-modern .required:after {
    content: '*';
    margin-left: 0.2em;
    color: #ea384c;
    font-size: 1em;
}
.select-multi {
    min-height: 66px;
    background: #fafdfe;
    padding-left: 2.3em !important;
    border-radius: 7px;
}
.select-multi:focus {
    border-color: #266ada;
    background: #f4fbff;
}
</style>
<div class="patient-form-container">
  <div class="form-header">
    <h1>
      <i class="fa-solid fa-user-plus icon-title"></i>
      Ajouter un nouveau patient
    </h1>
  </div>
  <form class="patient-form modern" id="addPatientForm" method="POST" action="{{ route('patients.store') }}">
    @csrf
    <div class="form-grid-modern" style="gap:32px 24px;">
      <div class="form-group-modern">
        <label for="nom" class="required">
          <i class="fa-solid fa-user"></i>
          Nom
        </label>
        <input type="text" id="nom" name="nom" required placeholder="Nom du patient" autocomplete="off">
      </div>
      <div class="form-group-modern">
        <label for="prenom" class="required">
          <i class="fa-solid fa-user"></i>
          Prénom
        </label>
        <input type="text" id="prenom" name="prenom" required placeholder="Prénom du patient" autocomplete="off">
      </div>
      <div class="form-group-modern">
        <label for="date_naissance" class="required">
          <i class="fa-solid fa-calendar"></i>
          Date de naissance
        </label>
        <input type="date" id="date_naissance" name="date_naissance" required>
      </div>
      <div class="form-group-modern">
        <label for="sexe" class="required">
          <i class="fa-solid fa-venus-mars"></i>
          Sexe
        </label>
        <select id="sexe" name="sexe" required>
          <option value="">Sélectionner</option>
          <option value="M">Masculin</option>
          <option value="F">Féminin</option>
          <option value="Autre">Autre</option>
        </select>
      </div>
      <div class="form-group-modern full-width">
        <label for="adresse">
          <i class="fa-solid fa-map-pin"></i>
          Adresse
        </label>
        <input type="text" id="adresse" name="adresse" placeholder="Adresse complète">
      </div>
      <div class="form-group-modern">
        <label for="telephone">
          <i class="fa-solid fa-phone"></i>
          Téléphone
        </label>
        <input type="tel" id="telephone" name="telephone" placeholder="Numéro de téléphone">
      </div>
      <div class="form-group-modern">
        <label for="email">
          <i class="fa-solid fa-envelope"></i>
          Email
        </label>
        <input type="email" id="email" name="email" placeholder="Email (optionnel)">
      </div>
      <div class="form-group-modern">
        <label for="numero_dossier" class="required">
          <i class="fa-solid fa-folder"></i>
          N° dossier
        </label>
        <input type="text" id="numero_dossier" name="numero_dossier" required placeholder="Numéro de dossier unique">
      </div>
      <div class="form-group-modern">
        <label for="taille">
          <i class="fa-solid fa-ruler-vertical"></i>
          Taille (cm)
        </label>
        <input type="number" min="0" step="0.01" id="taille" name="taille" placeholder="Ex: 175.5">
      </div>
      <div class="form-group-modern">
        <label for="poids">
          <i class="fa-solid fa-weight-scale"></i>
          Poids (kg)
        </label>
        <input type="number" min="0" step="0.01" id="poids" name="poids" placeholder="Ex: 61.4">
      </div>
      <div class="form-group-modern">
        <label for="service_id">
          <i class="fa-solid fa-hospital"></i>
          Service
        </label>
        <select id="service_id" name="service_id">
          <option value="">Non affecté</option>
          @foreach($services ?? [] as $service)
            <option value="{{ $service->id }}">{{ $service->name }}</option>
          @endforeach
        </select>
      </div>  
      <div class="form-group-modern full-width">
    <label class="form-label">
        <i class="fa-solid fa-microchip"></i>
        Sélectionnez un ou plusieurs capteurs
    </label>

    <div class="dropdown-sensor-wrapper">
        <div class="dropdown-sensor-toggle" onclick="toggleSensorDropdown()">
            <span id="sensor-selected-label">-- Sélectionner --</span>
            <i class="fa-solid fa-chevron-down"></i>
        </div>

        <div class="dropdown-sensor-menu" id="sensor-dropdown-menu">
            @forelse($sensors as $sensor)
                <label class="sensor-checkbox-item">
                    <input type="checkbox" name="sensors[]" value="{{ $sensor->id }}" onchange="updateSensorLabel()">
                    <span class="checkbox-icon">
                        <i class="fa-solid fa-microchip"></i>
                    </span>
                    <span class="sensor-info">
                        {{ $sensor->nom }}
                    </span>
                </label>
            @empty
                <div class="no-sensor-msg">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Aucun capteur disponible
                </div>
            @endforelse
        </div>
    </div>
</div>


    <div class="form-actions-modern">
        <button type="button" class="btn-modern btn-secondary" onclick="window.history.back()">
            <i class="fa-solid fa-arrow-left"></i>
            Annuler
        </button>
        <button type="submit" class="btn-modern btn-primary">
            <i class="fa-solid fa-circle-plus"></i>
            Ajouter
        </button>
    </div>
  </form>
</div>
<script src="https://kit.fontawesome.com/5bf5cbe1cf.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/forms.js') }}"></script>
<script>
    function toggleSensorDropdown() {
        const dropdown = document.getElementById('sensor-dropdown-menu');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function updateSensorLabel() {
        const checked = document.querySelectorAll('input[name="sensors[]"]:checked');
        const label = document.getElementById('sensor-selected-label');
        const names = Array.from(checked).map(cb => cb.parentElement.querySelector('.sensor-info').textContent.trim());
        label.textContent = names.length ? names.join(', ') : '-- Sélectionner --';
    }

    // Ferme si clic à l'extérieur
    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('.dropdown-sensor-wrapper');
        if (!wrapper.contains(e.target)) {
            document.getElementById('sensor-dropdown-menu').style.display = 'none';
        }
    });
</script>

@endsection
