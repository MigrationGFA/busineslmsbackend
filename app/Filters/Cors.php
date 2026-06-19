<?php
namespace App\Filters;
 
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
 
class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request->getMethod() === 'options') {
            $response = $this->corsResponse(service('response'), $request);
            return $response->setStatusCode(200);
        }
        // Add CORS headers for all other requests too
        $this->corsResponse(service('response'), $request);
    }
 
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $this->corsResponse($response, $request);
    }
 
    private function corsResponse(ResponseInterface $response, RequestInterface $request)
    {
        $config = config('CORS');
        $settings = $config->default ?? [];
 
        $origin = $request->getHeaderLine('Origin');
        $allowedOrigins = $settings['allowedOrigins'] ?? [];
        $allowedOriginPatterns = $settings['allowedOriginsPatterns'] ?? [];
 
        $allowOrigin = '';
        if ($origin !== '') {
            if (in_array($origin, $allowedOrigins, true)) {
                $allowOrigin = $origin;
            } else {
                foreach ($allowedOriginPatterns as $pattern) {
                    if (@preg_match('#\A' . $pattern . '\z#', $origin) === 1) {
                        $allowOrigin = $origin;
                        break;
                    }
                }
            }
        }
 
        if ($allowOrigin === '' && in_array('*', $allowedOrigins, true)) {
            $allowOrigin = '*';
        }
 
        if ($allowOrigin !== '') {
            $response->setHeader('Access-Control-Allow-Origin', $allowOrigin);
        }
 
        $allowedMethods = $settings['allowedMethods'] ?? [];
        if (!empty($allowedMethods)) {
            $response->setHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        }
 
        $allowedHeaders = $settings['allowedHeaders'] ?? [];
        if (!empty($allowedHeaders)) {
            $response->setHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        }
 
        $exposedHeaders = $settings['exposedHeaders'] ?? [];
        if (!empty($exposedHeaders)) {
            $response->setHeader('Access-Control-Expose-Headers', implode(', ', $exposedHeaders));
        }
 
        if (!empty($settings['supportsCredentials']) && $allowOrigin !== '*') {
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
        }
 
        if (!empty($settings['maxAge'])) {
            $response->setHeader('Access-Control-Max-Age', (string) $settings['maxAge']);
        }
 
        return $response;
    }
}
 
 