<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized', 'message' => 'Missing or invalid authorization header']);
        }
        
        $token = $matches[1];
        $jwtSecret = getenv('jwt.secret') ?: config('Encryption')->key;
        
        try {
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            
            // Attach user info to request
            $request->user = (array) $decoded;
            
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized', 'message' => 'Invalid or expired token']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
