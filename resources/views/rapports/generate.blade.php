@extends('layouts.app')

@section('title', 'HealthStatut - Générer Rapport')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<style>
.form-header i {
    color: #6E59A5;
    background: #ede7fd;
    border-radius: 50%;
    padding: 0.3em 0.4em;
}
</style>
<div class="report-form-container">
  <div class="form-header">
    <h1>
      <i class="fa-solid fa-file-alt icon-title"></i>
      Générer un rapport
    </h1>
  </div>

  <form class="report-form modern" id="generateReportForm" method="POST" action="{{ route('reports.generate') }}">
    @csrf
    <div class="form-grid-modern">
      <div class="form-group-modern">
        <label for="patient_id" class="required">
          <i class="fa-solid fa-user"></i>
          Patient
        </label>
        <select id="patient_id" name="patient_id" required>
          <option value="">Sélectionner un patient</option>
          @foreach($patients ?? [] as $patient)
            <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group-modern">
        <label for="report_type" class="required">
          <i class="fa-solid fa-clipboard-list"></i>
          Type de rapport
        </label>
        <select id="report_type" name="report_type" required>
          <option value="">Sélectionner</option>
          <option value="daily">Rapport journalier</option>
          <option value="weekly">Rapport hebdomadaire</option>
          <option value="monthly">Rapport mensuel</option>
        </select>
      </div>
      <div class="form-group-modern">
        <label for="start_date" class="required">
          <i class="fa-solid fa-calendar-day"></i>
          Date de début
        </label>
        <input type="date" id="start_date" name="start_date" required>
      </div>
      <div class="form-group-modern">
        <label for="end_date" class="required">
          <i class="fa-solid fa-calendar-check"></i>
          Date de fin
        </label>
        <input type="date" id="end_date" name="end_date" required>
      </div>
      <div class="form-group-modern full-width">
        <label for="notes">
          <i class="fa-solid fa-sticky-note"></i>
          Notes additionnelles
        </label>
        <textarea id="notes" name="notes" rows="4" placeholder="Commentaires, précisions..."></textarea>
      </div>
    </div>
    <div class="form-actions-modern">
      <button type="button" class="btn-modern btn-secondary" onclick="window.history.back()">
        <i class="fa-solid fa-arrow-left"></i> Annuler
      </button>
      <button type="submit" class="btn-modern btn-primary">
        <i class="fa-solid fa-file-export"></i> Générer le rapport
      </button>
    </div>
  </form>
</div>
<script src="https://kit.fontawesome.com/5bf5cbe1cf.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/forms.js') }}"></script>
@endsection
