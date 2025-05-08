<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nom' => 'admin',
                'description' => 'Administrateur du système avec tous les privilèges',
                'permissions' => json_encode(['all']),
            ],
            [
                'nom' => 'utilisateur',
                'description' => 'Utilisateur standard du système',
                'permissions' => json_encode(['view_patients', 'view_alerts']),
            ],
            [
                'nom' => 'medecin',
                'description' => 'Médecin avec accès aux dossiers des patients',
                'permissions' => json_encode(['view_patients', 'edit_patients', 'view_alerts', 'resolve_alerts']),
            ],
            [
                'nom' => 'patient',
                'description' => 'Patient avec accès limité à ses propres données',
                'permissions' => json_encode(['view_self']),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nom' => $role['nom']], // conditions pour chercher l'existence
                [ // données à insérer ou mettre à jour
                    'description' => $role['description'],
                    'permissions' => $role['permissions'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
