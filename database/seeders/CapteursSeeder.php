<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capteur; // Assure-toi que le modèle existe
use Illuminate\Support\Str;

class CapteursSeeder extends Seeder
{
    public function run(): void
    {
        $capteurs = [
            ['nom' => 'Fréquence cardiaque', 'type' => 'rythme_cardiaque'],
            ['nom' => 'Pression artérielle', 'type' => 'pression_arterielle'],
            ['nom' => 'Saturation en oxygène', 'type' => 'saturation_oxygene'],
            ['nom' => 'Fréquence respiratoire', 'type' => 'frequence_respiratoire'],
            ['nom' => 'Débit respiratoire', 'type' => 'debit_respiratoire'],
        ];

        foreach ($capteurs as $capteur) {
            \App\Models\Capteur::create([
                'nom' => $capteur['nom'],
                'type' => $capteur['type'],
                'numero_serie' => Str::upper(Str::random(10)),
                'modele' => 'Modèle X-' . rand(100, 999),
                'statut' => 'actif',
                'patient_id' => null,
                'derniere_maintenance' => now()->subDays(rand(0, 365)),
                'niveau_batterie' => rand(50, 100),
                'adresse_mac' => '00:1A:7D:' . Str::upper(Str::random(2)) . ':' . Str::upper(Str::random(2)) . ':' . Str::upper(Str::random(2)),
            ]);
        }
    }
}
