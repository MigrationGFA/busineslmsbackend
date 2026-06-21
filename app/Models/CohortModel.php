<?php

namespace App\Models;

use CodeIgniter\Model;

class CohortModel extends Model
{
    protected $table         = 'cohorts';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    protected $allowedFields = [
        'cohort',
        'slug',
        'state',
        'logo_url',
        'primary_color',
        'brochure_path',
        'price',
        'status',
        'created_at',
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    /**
     * Auto-derive slug from state + cohort number, e.g.
     * state=ogun, cohort=1 -> "ogun-cohort-1". This is for internal
     * reference only — the public URL uses `state`, not this slug.
     */
    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['state'], $data['data']['cohort']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = strtolower($data['data']['state']) . '-cohort-' . $data['data']['cohort'];
        }

        return $data;
    }

    /**
     * This is what the frontend actually calls: given a state from
     * the URL (/apply/ogun), find whichever cohort is currently open
     * for that state. Only one cohort per state should be 'open' at
     * a time — that's a data-entry rule, not enforced by the DB here.
     */
    public function findOpenByState(string $state): ?array
    {
        return $this->where('state', $state)
                    ->where('status', 'open')
                    ->first();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
