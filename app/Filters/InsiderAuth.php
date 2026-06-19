<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class InsiderAuth implements FilterInterface
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
            
            // Check if user is admin/analyst
            if (!isset($decoded->role) || !in_array($decoded->role, ['ADMIN', 'ANALYST'])) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['error' => 'Forbidden', 'message' => 'Insufficient permissions']);
            }
            
            // Check role requirement if specified
            if (!empty($arguments) && !in_array($decoded->role, $arguments)) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['error' => 'Forbidden', 'message' => 'Role not authorized']);
            }
            
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
