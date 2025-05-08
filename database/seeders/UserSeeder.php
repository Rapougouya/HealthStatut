<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Administrateur
            [
                'nom' => 'Admin',
                'prenom' => 'Système',
                'email' => 'admin123@gmail.com',
                'password' => Hash::make('admin1234'),
                'role_id' => 1,
                'service_id' => 1,
                'statut' => 'actif',
                'derniere_connexion' => now(),
                'theme' => 'light',
                'compact_mode' => false,
                'items_per_page' => 10,
                'notif_alertes_critiques' => true,
                'notif_alertes_hautes' => true,
                'notif_alertes_moyennes' => true,
                'email_alertes_critiques' => true,
                'email_rapports_quotidiens' => true,
                'email_mises_a_jour' => true,
                'avatar' => '/lovable-uploads/3bec0184-d9fb-47a0-b398-880496412872.png',
                'sexe' => 'Homme',
                'date_naissance' => null,
                'telephone' => null,
                'adresse' => null,
                'taille' => null,
                'poids' => null,
            ],
            // Médecin
            [
                'nom' => 'Dubois',
                'prenom' => 'Jean',
                'email' => 'medecin@gmail.com',
                'password' => Hash::make('medecin123'),
                'role_id' => 3,
                'service_id' => 2,
                'statut' => 'actif',
                'derniere_connexion' => now(),
                'theme' => 'light',
                'compact_mode' => false,
                'items_per_page' => 10,
                'notif_alertes_critiques' => true,
                'notif_alertes_hautes' => true,
                'notif_alertes_moyennes' => false,
                'email_alertes_critiques' => true,
                'email_rapports_quotidiens' => false,
                'email_mises_a_jour' => true,
                'avatar' => null,
                'sexe' => 'Homme',
                'date_naissance' => '1980-04-10',
                'telephone' => '0678123456',
                'adresse' => '456 Rue du Cœur',
                'taille' => 180,
                'poids' => 75,
            ],
            // Utilisateur standard
            [
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'email' => 'utilisateur@gmail.com',
                'password' => Hash::make('user123'),
                'role_id' => 2,
                'service_id' => 3,
                'statut' => 'actif',
                'derniere_connexion' => now(),
                'theme' => 'light',
                'compact_mode' => true,
                'items_per_page' => 10,
                'notif_alertes_critiques' => true,
                'notif_alertes_hautes' => false,
                'notif_alertes_moyennes' => false,
                'email_alertes_critiques' => false,
                'email_rapports_quotidiens' => true,
                'email_mises_a_jour' => false,
                'avatar' => null,
                'sexe' => 'Femme',
                'date_naissance' => '1992-07-22',
                'telephone' => '0789456123',
                'adresse' => '789 Avenue du Cerveau',
                'taille' => 170,
                'poids' => 60,
            ],
            // Patient
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'email' => 'patient@gmail.com',
                'password' => Hash::make('patient123'),
                'role_id' => 4,
                'service_id' => 1,
                'statut' => 'actif',
                'derniere_connexion' => now(),
                'theme' => 'light',
                'compact_mode' => true,
                'items_per_page' => 10,
                'notif_alertes_critiques' => true,
                'notif_alertes_hautes' => false,
                'notif_alertes_moyennes' => false,
                'email_alertes_critiques' => false,
                'email_rapports_quotidiens' => true,
                'email_mises_a_jour' => false,
                'avatar' => null,
                'sexe' => 'Femme',
                'date_naissance' => '1985-05-15',
                'telephone' => '0123456789',
                'adresse' => '123 Rue Exemple',
                'taille' => 165,
                'poids' => 65,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'password' => $user['password'],
                'role_id' => $user['role_id'],
                'service_id' => $user['service_id'],
                'statut' => $user['statut'],
                'derniere_connexion' => $user['derniere_connexion'],
                'theme' => $user['theme'],
                'compact_mode' => $user['compact_mode'],
                'items_per_page' => $user['items_per_page'],
                'notif_alertes_critiques' => $user['notif_alertes_critiques'],
                'notif_alertes_hautes' => $user['notif_alertes_hautes'],
                'notif_alertes_moyennes' => $user['notif_alertes_moyennes'],
                'email_alertes_critiques' => $user['email_alertes_critiques'],
                'email_rapports_quotidiens' => $user['email_rapports_quotidiens'],
                'email_mises_a_jour' => $user['email_mises_a_jour'],
                'avatar' => $user['avatar'],
                'sexe' => $user['sexe'],
                'date_naissance' => $user['date_naissance'],
                'telephone' => $user['telephone'],
                'adresse' => $user['adresse'],
                'taille' => $user['taille'],
                'poids' => $user['poids'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
