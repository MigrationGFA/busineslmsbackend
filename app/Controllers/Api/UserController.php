<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class UserController extends BaseController
{
    use ResponseTrait;

    /**
     * POST /api/apply/{state}/register
     * Expects a JSON body, e.g.:
     * { "full_name": "...", "email": "...", "company_name": "...", "job_title": "..." }
     */
    public function store(string $state)
    {
        $cohortModel = new CohortModel();
        $cohort      = $cohortModel->findOpenByState($state);

        if (! $cohort) {
            return $this->fail('No open registration for this state right now.', 403);
        }

        $input = $this->request->getJSON(true) ?? [];

        $rules = [
            'full_name'    => 'required|min_length[3]|max_length[150]',
            'email'        => 'required|valid_email',
            'company_name' => 'required|max_length[150]',
            'job_title'    => 'required|max_length[100]',
        ];

        if (! $this->validateData($input, $rules)) {
            return $this->fail($this->validator->getErrors(), 422);
        }

        $userModel = new UserModel();
        $email     = $input['email'];

        $existing = $userModel->findByEmail($email);

        if ($existing) {
            return $this->fail('This email is already registered.', 409);
        }

        $id = $userModel->insert([
            'cohort_id'      => $cohort['id'],
            'full_name'      => $input['full_name'],
            'email'          => $email,
            'company_name'   => $input['company_name'],
            'job_title'      => $input['job_title'],
            'payment_status' => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Payment initialization (Paystack/Flutterwave) happens here.
        return $this->respondCreated([
            'user_id' => $id,
            'amount'  => $cohort['price'],
            'message' => 'Registration captured. Proceed to payment.',
        ]);
    }
}
