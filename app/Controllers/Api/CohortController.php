<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use CodeIgniter\API\ResponseTrait;

class CohortController extends BaseController
{
    use ResponseTrait;

    public function show(string $slug)
    {
        $model  = new CohortModel();
        $cohort = $model->findBySlug($slug);

        if (! $cohort) {
            return $this->failNotFound('Cohort not found.');
        }

        if ($cohort['status'] !== 'open') {
            return $this->fail('Registration is not currently open for this cohort.', 403);
        }

        // Only expose what the frontend needs to render/theme the page
        return $this->respond([
            'slug'          => $cohort['slug'],
            'state'         => $cohort['state'],
            'logo_url'      => $cohort['logo_url'],
            'primary_color' => $cohort['primary_color'],
            'price'         => $cohort['price'],
        ]);
    }
}
