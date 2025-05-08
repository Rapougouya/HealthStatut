@extends('layouts.app')

@section('title', 'HealthStatut - Configurer Capteur')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<style>
.form-header i {
    color: #19c1a3;
    background: #e6fcfa;
    border-radius: 50%;
    padding: 0.32em 0.4em;
}
</style>
<div class="sensor-form-container">
  <div class="form-header">
    <h1>
      <i class="fa-solid fa-microchip icon-title"></i>
      Configurer un nouveau capteur
    </h1>
  </div>
  <form class="sensor-form" id="configureSensorForm" method="POST" action="{{ route('sensors.store') }}">
    @csrf
    <div class="form-grid-modern">
      <div class="form-group-modern">
        <label for="type" class="required"><i class="fa-solid fa-microchip"></i> Type de capteur</label>
        <select id="type" name="type" required>
          <option value="">Sélectionner</option>
          <option value="heartrate">Rythme cardiaque</option>
          <option value="temperature">Température</option>
          <option value="blood_pressure">Pression artérielle</option>
          <option value="oxygen">Saturation O₂</option>
        </select>
      </div>
      <div class="form-group-modern">
        <label for="modele" class="required"><i class="fa-solid fa-cogs"></i> Modèle</label>
        <input type="text" id="modele" name="modele" required placeholder="Modèle du capteur">
      </div>
      <div class="form-group-modern">
        <label for="serial" class="required"><i class="fa-solid fa-barcode"></i> Numéro de série</label>
        <input type="text" id="serial" name="serial" required placeholder="Numéro de série unique">
      </div>
      <div class="form-group-modern">
        <label for="patient_id"><i class="fa-solid fa-user"></i> Patient associé</label>
        <select id="patient_id" name="patient_id">
          <option value="">Aucun patient</option>
          @foreach($patients ?? [] as $patient)
            <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group-modern">
        <label for="min_value"><i class="fa-solid fa-arrow-down"></i> Seuil minimal</label>
        <input type="number" id="min_value" name="min_value" step="0.1" placeholder="Valeur minimale">
      </div>
      <div class="form-group-modern">
        <label for="max_value"><i class="fa-solid fa-arrow-up"></i> Seuil maximal</label>
        <input type="number" id="max_value" name="max_value" step="0.1" placeholder="Valeur maximale">
      </div>
    </div>
    <div class="form-actions-modern">
      <button type="button" class="btn-modern btn-secondary" onclick="window.history.back()">
        <i class="fa-solid fa-arrow-left"></i> Annuler
      </button>
      <button type="submit" class="btn-modern btn-primary">
        <i class="fa-solid fa-circle-plus"></i> Configurer le capteur
      </button>
    </div>
  </form>
</div>
<script src="https://kit.fontawesome.com/5bf5cbe1cf.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/forms.js') }}"></script>
@endsection
