<?php

namespace App\Models;

use CodeIgniter\Model;

class BrochureRequestModel extends Model
{
    protected $table         = 'brochure_requests';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    protected $allowedFields = [
        'cohort_id',
        'email',
        'created_at',
    ];

    public function findByEmailAndCohort(string $email, int $cohortId): ?array
    {
        return $this->where('email', $email)
                    ->where('cohort_id', $cohortId)
                    ->first();
    }
}
