<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\SigneVital;
use App\Models\Rapport;


class RapportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patientId = 1;  // Exemple d'ID de patient à récupérer, tu peux le passer en paramètre ou via la requête.

       // Récupérer le patient avec ses relations
       $selectedPatient = Patient::with([
           'prescriptions',
           'laboratoires',
           'antecedents',
           'allergies',
           'notes',
           'signesVitaux',
           'rapports',
       ])->findOrFail($patientId);

       // Dernier signe vital
       $dernierSigneVital = $selectedPatient->signesVitaux()->latest()->first();

       // Rapports récents (les 5 derniers rapports)
       $rapportsRecents = $selectedPatient->rapports()->latest()->take(5)->get();

       $signesVitaux = SigneVital::where('patient_id', $patientId)->latest()->get();

       // Laboratoires récents
       $laboratoires = $selectedPatient->laboratoires()->latest()->get();

       // Prescriptions récentes
       $prescriptions = $selectedPatient->prescriptions()->latest()->get();

       // Antécédents
       $antecedents = $selectedPatient->antecedents;

       // Allergies
       $allergies = $selectedPatient->allergies;

       // Notes
       $notes = $selectedPatient->notes()->latest()->get();

       // Passer toutes les données à la vue
       return view('rapports.index', compact(
           'selectedPatient', 
           'dernierSigneVital', 
           'rapportsRecents',
           'signesVitaux',
           'laboratoires',
           'prescriptions',
           'antecedents',
           'allergies',
           'notes'
       ));
    }

    public function generate()
{
    
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function medical() {
        return view('rapports.medical');
    }
    
    public function laboratory() {
        return view('rapports.laboratory');
    }
    
    public function vitals() {
        return view('rapports.vitals');
    }
    
    public function prescriptions() {
        return view('rapports.prescriptions');
    }
    
}
