<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use App\Models\RegistrantModel;
use CodeIgniter\API\ResponseTrait;

class RegistrationController extends BaseController
{
    use ResponseTrait;

    public function store(string $slug)
    {
        $cohortModel = new CohortModel();
        $cohort      = $cohortModel->findBySlug($slug);

        if (! $cohort || $cohort['status'] !== 'open') {
            return $this->fail('Registration is not open for this cohort.', 403);
        }

        $rules = [
            'full_name'    => 'required|min_length[3]|max_length[150]',
            'email'        => 'required|valid_email',
            'company_name' => 'required|max_length[150]',
            'job_title'    => 'required|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 422);
        }

        $registrantModel = new RegistrantModel();

        // Prevent duplicate registration for the same cohort
        $existing = $registrantModel->findByEmailAndCohort(
            $this->request->getPost('email'),
            $cohort['id'],
            'registration'
        );

        if ($existing) {
            return $this->fail('This email has already registered for this cohort.', 409);
        }

        $id = $registrantModel->insert([
            'cohort_id'      => $cohort['id'],
            'type'           => 'registration',
            'full_name'      => $this->request->getPost('full_name'),
            'email'          => $this->request->getPost('email'),
            'company_name'   => $this->request->getPost('company_name'),
            'job_title'      => $this->request->getPost('job_title'),
            'payment_status' => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Payment initialization (Paystack/Flutterwave) happens here.
        // Returning the registrant id + cohort price so frontend can
        // proceed to the payment step.
        return $this->respondCreated([
            'registrant_id' => $id,
            'amount'        => $cohort['price'],
            'message'       => 'Registration captured. Proceed to payment.',
        ]);
    }
}
