<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PrescriptionController extends Controller
{
        public function index()
    {
        $user = Auth::user();

        if ($user->role === 'medecin') {
            $prescriptions = Prescription::where('medecin', $user->name)->get();
        } elseif ($user->role === 'admin') {
            $prescriptions = Prescription::all();
        } else {
            abort(403);
        }

        return view('prescriptions.index', compact('prescriptions'));
    }

   public function mesPrescriptions()
   {
       $user = Auth::user();
       if ($user->role !== 'patient') abort(403);

       $prescriptions = Prescription::where('patient_id', $user->id)->get();
       return view('prescriptions.mes', compact('prescriptions'));
   }
}