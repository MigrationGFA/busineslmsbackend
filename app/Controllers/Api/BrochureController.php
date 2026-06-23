<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use App\Models\BrochureRequestModel;
use CodeIgniter\API\ResponseTrait;
use Throwable;

class BrochureController extends BaseController
{
    use ResponseTrait;
    use CallsSmtpApi;

    /**
     * POST /api/apply/{state}/brochure
     * Expects a JSON body, e.g.: { "email": "..." }
     */
    public function send(string $state)
    {
        try {
            $cohortModel = new CohortModel();
            $cohort      = $cohortModel->findOpenByState($state);

            if (! $cohort) {
                return $this->failNotFound('Brochure is not available right now. Please check back soon.');
            }

            if (empty($cohort['brochure_path'])) {
                return $this->fail('Brochure is not available right now. Please check back soon.', 404);
            }

            $input = $this->request->getJSON(true);

            if (! is_array($input)) {
                return $this->fail('Something went wrong with your submission. Please try again.', 400);
            }

            if (! $this->validateData($input, ['email' => 'required|valid_email'])) {
                return $this->fail('Please enter a valid email address.', 422);
            }

            $email = $input['email'];

            $brochureModel = new BrochureRequestModel();

            // Log the lead even on repeat requests; only block duplicate
            // logging, the email itself still gets resent below.
            $existing = $brochureModel->findByEmailAndCohort($email, $cohort['id']);

            if (! $existing) {
                $inserted = $brochureModel->insert([
                    'cohort_id'  => $cohort['id'],
                    'email'      => $email,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if (! $inserted) {
                    log_message('error', 'BrochureController::send - failed to log brochure request for ' . $email);
                }
            }

            $sent = $this->sendBrochureEmail($email, $cohort);

            if (! $sent) {
                return $this->fail('Could not send the brochure. Please try again shortly.', 500);
            }

            return $this->respond(['message' => 'Brochure sent! Please check your email.']);
        } catch (Throwable $e) {
            log_message('error', 'BrochureController::send - ' . $e->getMessage());

            return $this->fail('Something went wrong. Please try again shortly.', 500);
        }
    }

    private function sendBrochureEmail(string $toEmail, array $cohort): bool
    {
        try {
            $brochureUrl = rtrim(base_url(), '/') . '/' . ltrim($cohort['brochure_path'], '/');

            $subject = 'Your Brochure - ' . $cohort['state'] . ' Cohort';
            $logoUrl = rtrim(base_url(), '/') . '/assets/RemsanaLogoBlue.png';

            $message = "<div style=\"text-align:center; margin-bottom:24px;\">"
                . "<img src=\"{$logoUrl}\" alt=\"Remsana\" style=\"max-width:180px; height:auto;\">"
                . "</div>"
                . "Hi,<br><br>"
                . "Thanks for your interest in the program.<br>"
                . "You can download your brochure here: "
                . "<a href=\"{$brochureUrl}\">{$brochureUrl}</a><br><br>"
                . "Best regards.";

            return $this->callSmtpApi($toEmail, $subject, $message);
        } catch (Throwable $e) {
            log_message('error', 'BrochureController::sendBrochureEmail - ' . $e->getMessage());

            return false;
        }
    }
}
