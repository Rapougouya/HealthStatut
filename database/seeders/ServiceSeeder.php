<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cardiologie',
                'description' => 'Service de cardiologie',
                'category_id' => 1,
                'cost' => 0,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Neurologie',
                'description' => 'Service de neurologie',
                'category_id' => 1,
                'cost' => 0,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Pédiatrie',
                'description' => 'Service de pédiatrie',
                'category_id' => 1,
                'cost' => 0,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Radiologie',
                'description' => 'Service de radiologie et d\'imagerie',
                'category_id' => 2,
                'cost' => 0,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Urgences',
                'description' => 'Service des urgences',
                'category_id' => 3,
                'cost' => 0,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
