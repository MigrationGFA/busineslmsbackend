<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrantModel extends Model
{
    protected $table         = 'registrants';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    protected $allowedFields = [
        'cohort_id',
        'type',
        'email',
        'full_name',
        'company_name',
        'job_title',
        'payment_status',
        'payment_reference',
        'created_at',
    ];

    protected $validationRules = [
        'cohort_id' => 'required|integer',
        'email'     => 'required|valid_email',
    ];

    public function findByEmailAndCohort(string $email, int $cohortId, string $type): ?array
    {
        return $this->where('email', $email)
                    ->where('cohort_id', $cohortId)
                    ->where('type', $type)
                    ->first();
    }
}
