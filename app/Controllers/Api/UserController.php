<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CohortModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Throwable;

class UserController extends BaseController
{
    use ResponseTrait;
    use CallsSmtpApi;

    /**
     * POST /api/apply/{state}/register
     * Expects a JSON body, e.g.:
     * { "full_name": "...", "email": "...", "company_name": "...", "job_title": "..." }
     */
    public function store(string $state)
    {
        try {
            $cohortModel = new CohortModel();
            $cohort      = $cohortModel->findOpenByState($state);

            if (! $cohort) {
                return $this->fail('Registration is not open right now. Please check back soon.', 403);
            }

            $input = $this->request->getJSON(true);

            if (! is_array($input)) {
                return $this->fail('Something went wrong with your submission. Please try again.', 400);
            }

            $rules = [
                'full_name'     => 'required|min_length[3]|max_length[150]',
                'email'         => 'required|valid_email',
                'company_name'  => 'required|max_length[150]',
                'job_title'     => 'required|max_length[100]',
                'hear_about_us' => 'required|max_length[200]',
            ];

            if (! $this->validateData($input, $rules)) {
                return $this->fail('Please check your details and try again.', 422);
            }

            $userModel = new UserModel();
            $email     = $input['email'];

            $existing = $userModel->findByEmail($email);

            if ($existing) {
                return $this->fail('This email has already been used to apply.', 409);
            }

            $id = $userModel->insert([
                'cohort_id'      => $cohort['id'],
                'full_name'      => $input['full_name'],
                'email'          => $email,
                'company_name'   => $input['company_name'],
                'job_title'      => $input['job_title'],
                'hear_about_us'  => $input['hear_about_us'],
                'payment_status' => 'pending',
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            if (! $id) {
                return $this->fail('Could not submit your application. Please try again.', 500);
            }

            // Send confirmation email. If this fails, the registration
            // still stands — we just log it rather than fail the whole
            // request, since the applicant shouldn't see an error for
            // something that already succeeded.
            $this->sendConfirmationEmail($input['full_name'], $email, $input['company_name'], $input['job_title'], $cohort['cohort']);

            // Payment initialization (Paystack/Flutterwave) happens here.
            return $this->respondCreated([
                'message' => 'Your application is received. Thanks for applying! Please check your email — our team will review your application and reach out to you shortly.',
            ]);
        } catch (Throwable $e) {
            log_message('error', 'UserController::store - ' . $e->getMessage());

            return $this->fail('Something went wrong. Please try again shortly.', 500);
        }
    }

    private function sendConfirmationEmail(string $fullName, string $email, string $companyName, string $jobTitle, int $cohortNumber): void
    {
        try {
            $subject = 'Your Application to the Remsana AI for Founders & Business Owners Programme';

            // Logo lives in CI4's public/ folder, so base_url() builds the
            // correct working link automatically, same pattern as the brochure file.
            $logoUrl = rtrim(base_url(), '/') . '/assets/RemsanaLogoBlue.png';

            $message = "<div style=\"text-align:center; margin-bottom:24px;\">"
                . "<img src=\"{$logoUrl}\" alt=\"Remsana\" style=\"max-width:180px; height:auto;\">"
                . "</div>"

                . "<p>Dear {$fullName},</p>"

                . "<p>Thank you for applying to the <strong>Remsana AI for Founders & Business Owners Programme</strong>.</p>"

                . "<p>We are delighted to confirm that we have received your application and appreciate your "
                . "interest in joining this exclusive <strong>100-Day Business Transformation Journey</strong> "
                . "designed for founders, CEOs, business owners, and executive leaders.</p>"

                . "<p>At Remsana Academy, we believe that Artificial Intelligence is no longer just a technology "
                . "trend—it is a business imperative. This programme has been carefully designed to help business "
                . "leaders move beyond theory and learn how to strategically integrate AI into their organizations "
                . "to improve productivity, optimize operations, enhance decision-making, and drive sustainable "
                . "growth.</p>"

                . "<p><strong>What Happens Next?</strong></p>"

                . "<p>Our admissions team will review your application based on the programme's eligibility "
                . "criteria, including business maturity, leadership commitment, growth potential, and readiness "
                . "to implement AI-driven transformation within your organization.</p>"

                . "<p>Applications are reviewed on a rolling basis, and shortlisted candidates will be invited to "
                . "participate in a brief assessment and/or interview session as part of the selection process.</p>"

                . "<p><strong>While You Wait</strong></p>"

                . "<p>We encourage you to begin thinking about the following questions:</p>"

                . "<ul>"
                . "<li>What are the biggest operational challenges currently facing your business?</li>"
                . "<li>Which business processes consume the most time and resources?</li>"
                . "<li>Where do you believe AI could create the greatest impact within your organization?</li>"
                . "<li>What would business transformation look like for your company over the next 12 months?</li>"
                . "</ul>"

                . "<p>Reflecting on these questions will help you maximize your experience should you be selected "
                . "for the programme.</p>"

                . "<p><strong>About the Programme</strong></p>"

                . "<p>The Remsana AI for Founders & Business Owners Programme combines:</p>"

                . "<ul>"
                . "<li>Executive Masterclasses</li>"
                . "<li>AI Implementation Labs</li>"
                . "<li>CEO Accountability Sessions</li>"
                . "<li>Expert Coaching & Office Hours</li>"
                . "<li>Peer Advisory Circles</li>"
                . "<li>Business Transformation Projects</li>"
                . "</ul>"

                . "<p>Over the course of <strong>100 days</strong>, participants will develop and implement "
                . "practical AI strategies tailored to their businesses while receiving guidance from industry "
                . "experts and experienced practitioners.</p>"

                . "<p><strong>Key Dates</strong></p>"

                . "<p>"
                . "Application Deadline: <strong>31st July 2026</strong><br>"
                . "Review &amp; Selection Period: <strong>Ongoing</strong><br>"
                . "Programme Commencement: <strong>3rd August 2026</strong>"
                . "</p>"

                . "<p>Should you have any questions regarding your application, please feel free to contact us "
                . "at <strong>info@remsana.com</strong>.</p>"

                . "<p>Thank you once again for your interest in Remsana Academy. We look forward to learning "
                . "more about you and your business.</p>"

                . "<p>Warm regards,<br>"
                . "<strong>The Remsana Academy Team</strong><br>"
                . "Powered by GFA Technologies<br>"
                . "<em>Building Smarter Businesses. Enabling Sustainable Growth.</em></p>";

            $sent = $this->callSmtpApi($email, $subject, $message);

            if (! $sent) {
                log_message('error', 'UserController::sendConfirmationEmail - failed to send to ' . $email);
            }
        } catch (Throwable $e) {
            log_message('error', 'UserController::sendConfirmationEmail - ' . $e->getMessage());
        }
    }
}
