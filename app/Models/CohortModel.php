<?php

namespace App\Models;

use CodeIgniter\Model;

class CohortModel extends Model
{
    protected $table         = 'cohorts';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false; // we manage created_at manually, no updated_at needed
    protected $returnType    = 'array';

    protected $allowedFields = [
        'slug',
        'state',
        'logo_url',
        'primary_color',
        'brochure_path',
        'price',
        'status',
        'created_at',
    ];

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}