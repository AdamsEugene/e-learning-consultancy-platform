<?php 
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Throttle implements FilterInterface {

    public function before(RequestInterface $request, $arguments = null) {
        
        $throttler = Services::throttler();

        if($throttler->check(md5($request->getIPAddress()), 10000, MINUTE) === false) {

            Services::response()->setHeader('X-RateLimit-Limit', 100);
            Services::response()->setHeader('Content-Type', 'application/json');
            Services::response()->setBody(json_encode([
                'status' => 429,
                'message' => 'Too many requests'
            ]));

            return Services::response()->setStatusCode(429);

        }

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {

    }

}
?>