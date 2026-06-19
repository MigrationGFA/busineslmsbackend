<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CohortSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'slug'           => 'lagos-cohort-1',
                'state'          => 'Lagos',
                'logo_url'       => '/assets/logos/lagos.png',
                'primary_color'  => '#1A73E8',
                'brochure_path'  => 'brochures/lagos.pdf',
                'price'          => 50000.00,
                'status'         => 'open',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug'           => 'abuja-cohort-1',
                'state'          => 'Abuja',
                'logo_url'       => '/assets/logos/abuja.png',
                'primary_color'  => '#34A853',
                'brochure_path'  => 'brochures/abuja.pdf',
                'price'          => 50000.00,
                'status'         => 'draft',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('cohorts')->insertBatch($data);
    }
}
