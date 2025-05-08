<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Consultations',
                'description' => 'Services de consultation médicale',
            ],
            [
                'name' => 'Examens',
                'description' => 'Services d\'examens et d\'imagerie médicale',
            ],
            [
                'name' => 'Soins d\'urgence',
                'description' => 'Services de soins d\'urgence',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('service_categories')->insert([
                'name' => $category['name'],
                'description' => $category['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}