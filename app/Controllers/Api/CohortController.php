<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use CodeIgniter\API\ResponseTrait;

class CohortController extends BaseController
{
    use ResponseTrait;

    /**
     * GET /api/apply/{state}
     * Frontend calls this with whatever state is in the URL.
     * We find whichever cohort is currently open for that state.
     */
    public function showByState(string $state)
    {
        $model  = new CohortModel();
        $cohort = $model->findOpenByState($state);

        if (! $cohort) {
            return $this->failNotFound('No open registration for this state right now.');
        }

        // Only expose what the frontend needs to render/theme the page
        return $this->respond([
            'cohort'        => $cohort['cohort'],
            'state'         => $cohort['state'],
            'logo_url'      => $cohort['logo_url'],
            'primary_color' => $cohort['primary_color'],
            'price'         => $cohort['price'],
        ]);
    }
}
