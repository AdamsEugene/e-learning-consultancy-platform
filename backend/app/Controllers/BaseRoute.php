<?php

namespace App\Controllers;

use App\Controllers\Api;
use CodeIgniter\API\ResponseTrait;

class BaseRoute extends BaseController
{

    use ResponseTrait;

    public $statusCode = 200;

    /**
     * Control the base route
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function control()
    {

        // get the request path
        $req_path = $this->request->getPath();

        // split the request path
        $splitPath = explode('/', $req_path);

        // create a new object of the api controller
        $api = new Api();

        // set the request path
        $api->requestPath = empty($req_path) ? 'unknown/path' : $req_path;

        // set the request payload
        $api->requestPayload = $this->fetchPayload();

        // set the request method
        $api->requestMethod = $this->request->getMethod();

        // set the base route request
        $api->fromBaseRoute = true;

        // process the request handlers
        $finalRequest = $api->index('', $splitPath[0], $splitPath[1] ?? '', $splitPath[2] ?? '');

        // set the status code
        $this->statusCode = $finalRequest['statusCode'];

        unset($finalRequest['statusCode']);

        // return the response
        return $this->respond($finalRequest, $this->statusCode);

    }

    /**
     * Fetch the request payload
     * 
     * @return array
     */
    public function fetchPayload() {
        // get the variables
        $jsonData =  !empty($this->request) && method_exists($this->request, 'getJSON') ? $this->request->getJSON(true) : [];

        // return the payload
        $fullPayload = $_GET + $_POST + $jsonData;

        // return the payload
        return empty($fullPayload) ? ['unknown' => 'payload'] : $fullPayload;
    }

}
