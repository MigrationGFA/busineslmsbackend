<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use App\Models\RegistrantModel;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class BrochureController extends BaseController
{
    use ResponseTrait;

    public function send(string $slug)
    {
        $cohortModel = new CohortModel();
        $cohort      = $cohortModel->findBySlug($slug);

        if (! $cohort) {
            return $this->failNotFound('Cohort not found.');
        }

        if (empty($cohort['brochure_path'])) {
            return $this->fail('No brochure available for this cohort yet.', 404);
        }

        if (! $this->validate(['email' => 'required|valid_email'])) {
            return $this->fail($this->validator->getErrors(), 422);
        }

        $email = $this->request->getPost('email');

        $registrantModel = new RegistrantModel();

        // Log the lead even if they request the brochure more than once;
        // only block exact duplicate logging, still resend the email.
        $existing = $registrantModel->findByEmailAndCohort($email, $cohort['id'], 'brochure');

        if (! $existing) {
            $registrantModel->insert([
                'cohort_id'  => $cohort['id'],
                'type'       => 'brochure',
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
        $emailService = Services::email();

        $brochureFullPath = WRITEPATH . '../public/' . $cohort['brochure_path'];

        $emailService->setTo($toEmail);
        $emailService->setFrom('no-reply@yourdomain.com', 'Your Program Name');
        $emailService->setSubject('Your Brochure - ' . $cohort['state'] . ' Cohort');
        $emailService->setMessage(
            "Hi,\n\nThanks for your interest in the {$cohort['state']} cohort. "
            . "Your brochure is attached to this email.\n\nBest regards."
        );

        if (file_exists($brochureFullPath)) {
            $emailService->attach($brochureFullPath);
        }

        return $emailService->send();
    }
}
