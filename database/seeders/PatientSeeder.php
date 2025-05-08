<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run()
    {
        $patients = [
            [
                'nom' => 'KABORE',
                'prenom' => 'Issa',
                'numero_dossier' => 'DOS001',
                'adresse' => 'Koudougou',
                'date_naissance' => '1990-05-10',
                'email' => 'issa.kabore@example.com',
                'poids' => 70.2,
                'taille' => 175.5,
                'sexe' => 'M',
                'telephone' => '70112233',
                'service_id' => 1,
            ],
            [
                'nom' => 'OUEDRAOGO',
                'prenom' => 'Aïcha',
                'numero_dossier' => 'DOS002',
                'adresse' => 'Ouagadougou',
                'date_naissance' => '1988-11-25',
                'email' => 'aicha.ouedraogo@example.com',
                'poids' => 60.5,
                'taille' => 160,
                'sexe' => 'F',
                'telephone' => '65112233',
                'service_id' => 2,
            ],
        ];

        foreach ($patients as $patient) {
            Patient::updateOrCreate(
                ['numero_dossier' => $patient['numero_dossier']],
                $patient
            );
        }
    }
}
