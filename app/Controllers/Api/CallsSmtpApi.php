<?php

namespace App\Controllers\Api;

trait CallsSmtpApi
{
    /**
     * Calls the existing gfa-tech.com/smtp email API directly.
     * Same 4 parameters used elsewhere: recipient_email, subject, fromName, message.
     */
    private function callSmtpApi(string $to, string $subject, string $message, string $fromName = 'REMSANA'): bool
    {
        $apiUrl = getenv('email.api.url') ?: 'https://gfa-tech.com/smtp/';

        $payload = json_encode([
            'recipient_email' => $to,
            'subject'         => $subject,
            'fromName'        => $fromName,
            'message'         => $message,
        ]);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', static::class . '::callSmtpApi - curl error: ' . $curlError);
            return false;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            log_message('error', static::class . '::callSmtpApi - failed [' . $httpCode . ']: ' . $response);
            return false;
        }

        return true;
    }
}
