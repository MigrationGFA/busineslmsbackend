<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CohortSeeder extends Seeder
{
    public function run()
    {
        // Seeders bypass the model's auto-slug hook, so slug is typed manually here.
        $data = [
            [
                'cohort'        => 1,
                'slug'          => 'ogun-cohort-1',
                'state'         => 'ogun',
                'logo_url'      => '/assets/logos/ogun.png',
                'primary_color' => '#E63946',
                'brochure_path' => 'brochures/ogun.pdf',
                'price'         => 50000.00,
                'status'        => 'open',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('cohorts')->insertBatch($data);
    }
}
