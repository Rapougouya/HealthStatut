<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceManagementController extends Controller
{
    /**
     * Enregistre un nouveau service
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'Service créé avec succès.');
    }

    /**
     * Met à jour un service existant
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'Service mis à jour avec succès.');
    }
}
