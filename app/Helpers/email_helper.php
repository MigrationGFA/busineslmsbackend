<?php

if (!function_exists('send_email_via_api')) {
    function send_email_via_api(string $to, string $subject, string $message, string $fromName = null): bool
    {
        $apiUrl   = getenv('email.api.url') ?: 'https://gfa-tech.com/smtp/';
        $fromName = $fromName ?: (getenv('email.fromName') ?: 'REMSANA');

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

        if ($curlError) {
            log_message('error', 'Email API curl error: ' . $curlError);
            return false;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'Email API failed [' . $httpCode . ']: ' . $response);
            return false;
        }

        return true;
    }
}
