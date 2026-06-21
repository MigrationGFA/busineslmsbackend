<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use App\Models\BrochureRequestModel;
use CodeIgniter\API\ResponseTrait;

class BrochureController extends BaseController
{
    use ResponseTrait;

    /**
     * POST /api/apply/{state}/brochure
     * Expects a JSON body, e.g.: { "email": "..." }
     */
    public function send(string $state)
    {
        $cohortModel = new CohortModel();
        $cohort      = $cohortModel->findOpenByState($state);

        if (! $cohort) {
            return $this->failNotFound('No open registration for this state right now.');
        }

        if (empty($cohort['brochure_path'])) {
            return $this->fail('No brochure available for this cohort yet.', 404);
        }

        $input = $this->request->getJSON(true) ?? [];

        if (! $this->validateData($input, ['email' => 'required|valid_email'])) {
            return $this->fail($this->validator->getErrors(), 422);
        }

        $email = $input['email'];

        $brochureModel = new BrochureRequestModel();

        // Log the lead even on repeat requests; only block duplicate
        // logging, the email itself still gets resent below.
        $existing = $brochureModel->findByEmailAndCohort($email, $cohort['id']);

        if (! $existing) {
            $brochureModel->insert([
                'cohort_id'  => $cohort['id'],
                'email'      => $email,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $sent = $this->sendBrochureEmail($email, $cohort);

        if (! $sent) {
            return $this->fail('Could not send the brochure email. Please try again.', 500);
        }

        return $this->respond(['message' => 'Brochure sent to your email.']);
    }

    private function sendBrochureEmail(string $toEmail, array $cohort): bool
    {
        $brochureUrl = rtrim(base_url(), '/') . '/' . ltrim($cohort['brochure_path'], '/');

        $subject = 'Your Brochure - ' . $cohort['state'] . ' Cohort';

        $message = "Hi,<br><br>"
            . "Thanks for your interest in the {$cohort['state']} cohort.<br>"
            . "You can download your brochure here: "
            . "<a href=\"{$brochureUrl}\">{$brochureUrl}</a><br><br>"
            . "Best regards.";

        return send_email_via_api($toEmail, $subject, $message);
    }
}
