<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Message;
use App\Libraries\Caching;
use App\Libraries\Routing;
class Api extends BaseController
{

    use ResponseTrait;

    // request path
    private $req_path;

    // set the request path
    public $requestPath;

    // set the request payload
    public $requestPayload;

    // set the request method
    public $requestMethod;

    // set the base route request
    public $fromBaseRoute = false;

    // set the item id
    public $uniqueId;

    // set the unique id
    public $mainRawId;

    // set the cache use
    public $cacheUse = false;

    // set the parsed method
    public $parsedMethod;

    /**
     * @return \CodeIgniter\HTTP\Response
     */
    public function index($file = '', $class = '', $method = 'list', $uniqueId = '')
    {

        // create the database structure
        if(configs('db_group') == 'tests') {
            createDatabaseStructure();
        }

        // get the request path
        $this->req_path = !empty($this->requestPath) ? $this->requestPath : $this->request->getPath();

        // restructure the request path by appending api if not present
        if(strpos($this->req_path, 'api/') === false) {
            $this->req_path = 'api/' . $this->req_path;
        }

        // get the variables
        $jsonData = !empty($this->requestPayload) ? [] : (!empty($this->request) && method_exists($this->request, 'getJSON') ? $this->request->getJSON(true) : []);
        $payload = !empty($this->requestPayload) ? $this->requestPayload : ($_GET + $_POST + $jsonData);

        if(isset($payload['search'])) {
            $payload['search'] = urldecode(trim($payload['search']));
        }

        // set the parsed method
        $this->parsedMethod = $method;

        // set the default method
        $method = empty($method) ? 'list' : $method;

        // set the item id
        $this->uniqueId = $uniqueId;

        // escape the payload
        $payload = array_map('esc', $payload);
        $payload['ipaddress'] = !empty($this->request) ? $this->request->getIPAddress() : '';

        // set the request method
        $this->requestMethod = !empty($this->requestMethod) ? $this->requestMethod : $this->request->getMethod();
        $this->requestMethod = strtoupper($this->requestMethod);

        // process the request handlers
        $handler = $this->processHandler($class, $method, $payload, $uniqueId);

        // add the payload, path and status code to the handler
        // $handler['additional']['path'] = $this->req_path;
        // $handler['additional']['payload'] = $payload;

        if($this->fromBaseRoute) {
            $handler['statusCode'] = $this->statusCode;
        } elseif(isset($handler['statusCode'])) {
            unset($handler['statusCode']);
        }

        // remove the invalidate prop if it is set
        foreach(['invalidate'] as $key) {
            if(isset($handler[$key])) {
                unset($handler[$key]);
            }
        }

        // remove the message prop if it is set and the message is the same as the status
        if(!empty($handler['message']) && !empty($handler['status']) && $handler['message'] == $handler['status']) {
            unset($handler['message']);
        }

        // remove the record and unique id props if they are empty
        foreach(['record', 'uniqueId', 'pagination'] as $key) {
            if(isset($handler[$key]) && empty($handler[$key])) {
                unset($handler[$key]);
            }
        }

        // set the request id
        $handler['requestId'] = random_string('alnum', 12);
        $handler['cused'] = $this->cacheUse; // whether the cache was used or not
        
        // return the response
        return $this->fromBaseRoute ? $handler : $this->respond($handler, $this->statusCode);
        
    }

    /**
     * Process the request handler
     * 
     * @param string $class
     * @param string $method
     * @param array $payload
     * @param string $uniqueId
     * 
     * @return array
     */
    private function processHandler($class, $method = 'list', $payload = [], $uniqueId = '') {

        // get the start time
        $start_time = date('Y-m-d H:i:s');

        // set the main raw id
        $this->mainRawId = $uniqueId;

        if(isset($payload['debugger'])) {
            remove_configs();
        }
            
        // get the class name
        $classname = "\\App\\Controllers\\".ucfirst($class).'\\'.ucfirst($class);
        
        // confirm if the class actually exists
        if(!class_exists($classname)) {
            $this->statusCode = 400;
            return [
                'status' => 'error',
                'message' => 'Sorry! The model you are trying to access is unavailable.'
            ];
        }

        // decode the payload
        foreach(['token', 'password', 'password_confirm', 'secret'] as $key) {
            if(isset($payload[$key])) {
                $payload[$key] = html_entity_decode($payload[$key]);
            }
        }

        // convert the limit and offset to pageSize and pageNumber
        foreach(['limit' => 'pageSize', 'offset' => 'pageNumber'] as $key => $value) {  
            if(isset($payload[$value])) {
                $payload[$key] = $payload[$value];
            }
        }

        // convert the and deviceType to integers
        foreach(['deviceType', 'accountId', 'userId', 'courseId', 'categoryId', 'instructorId', 'studentId'] as $key) {
            if(isset($payload[$key])) {
                $payload[$key] = (int) $payload[$key];
            }
        }

        // convert the filters to a valid format
        if(isset($payload['filters'])) {
            $split = explode(';', $payload['filters']);
            $filterss = '';
            foreach($split as $filters) {
                $filters = explode('==', $filters);
                if($filters[0] == 'ipAddress') {
                    $split2 = explode(',', $filters[1]);
                    $converted = '';
                    foreach($split2 as $filters2) {
                        $converted .= trim(strlen($filters2) == 32 ? $filters2 :  md5($filters2)) . ",";
                    }
                    $filters[1] = rtrim($converted, ',');
                }
                $filterss .= html_entity_decode("{$filters[0]}".(isset($filters[1]) ? "=={$filters[1]}" : "").";");
            }
            $payload['filters'] = !empty($filterss) ? rtrim($filterss, ';') : $payload['filters'];
        }
        
        // get the split path
        $splitPath = explode("/", rtrim($this->req_path, '/?'));

        // get the message object
        $messageObject = new Message();
        $messageObject->headers();

        // get the token
        if(!in_array('token', array_keys($payload))) {
            $getToken = $messageObject?->header('Authorization')?->getValueLine();
            if(!empty($getToken)) {
                $payload['token'] = trim(str_replace('Bearer ', '', $getToken));
            }
        }

        if(!in_array('agent', array_keys($payload))) {
            $payload['agent'] = $messageObject->header('User-Agent')?->getValueLine();
        }

        // if the request method is POST and the method is list, set the method to create
        if(($this->requestMethod == 'POST') && ($method == 'list') && empty($this->parsedMethod)) {
            $method = 'create';
        }

        // create a new class for handling the resource
        $classObject = new $classname();

        // set the unique id
        $classObject->uniqueId = $this->uniqueId;
        
        // if the method is a number, set it to view
        if(preg_match('/^[0-9]+$/', $method)) {
            // set the unique id
            $classObject->uniqueId = (int)$method;
            $this->uniqueId = (int)$method;

            // set the method
            if(($this->requestMethod == 'GET')) {
                $method = 'view';
            }
            else if(in_array($this->requestMethod, ['POST', 'PUT'])) {
                $method = 'update';
            }
            else if(($this->requestMethod == 'DELETE')) {
                $method = 'delete';
            }
        }

        // if the request method is PUT or DELETE and the unique id is empty, return an error
        if(in_array($this->requestMethod, ['PUT', 'DELETE']) && empty($this->uniqueId)) {
            $this->statusCode = 400;
            return [
                'status' => 'error',
                'uniqueId' => $uniqueId,
                'message' => 'Sorry! The record id is required to perform this action.'
            ];
        }

        // create the cache object
        $cacheObject = !empty($classObject->cacheObject) ? $classObject->cacheObject : new Caching();

        // validate the request
        $validObject = new RequestHandler();
        $validObject->uniqueId = $this->uniqueId;
        $validObject->cacheObject = $cacheObject;

        // set the cache object
        $classObject->cacheObject = $cacheObject;

        // validate the request
        $validPayload = $validObject->validateRequest($class, $method, $this->requestMethod, $payload, $classObject, $splitPath);
       
        // return the validation error
        if(!is_bool($validPayload)) {
            $this->statusCode = $validObject->statusCode;
            
            return $validPayload;
        }

        try {

            // if the unique id is set, set the method to view, update or delete
            if($this->uniqueId && preg_match('/^[0-9]+$/', $method)) {
                if(($this->requestMethod == 'GET')) {
                    $method = 'view';
                }
                else if(in_array($this->requestMethod, ['POST', 'PUT'])) {
                    $method = 'update';
                }
                else if(($this->requestMethod == 'DELETE')) {
                    $method = 'delete';
                }
            }

            // confirm if the method exists
            if(!method_exists($classObject, $method)) {
                // set the status code
                $this->statusCode = 400;
                return [
                    'status' => 'error',
                    'message' => 'Sorry! The method you are trying to access is unavailable.'
                ];
            }

            // set the payload
            $classObject->payload = $validObject->parsedPayload;
            $classObject->mainRawId = $this->mainRawId;
            
            // manipulate the payload
            $payload = $validObject->manipulatePayload($payload, $class, $this->uniqueId, $this->mainRawId);

            // set the current user
            $cacheObject->currentUser = $classObject->currentUser;
            
            // create the cache object
            $loadCache = $validObject->cacheObject->handle($class, $method, $payload, 'get', [], $classObject->currentUser);

            // set the incoming payload
            $classObject->incomingPayload = $payload;

            if(isset($payload['account_id'])) {
                $cacheObject->accountId = $payload['account_id'];
            }

            // set the current user
            if(!empty($classObject->currentUser)) {
                // set it to the base controller too
                $validObject->cacheObject->currentUser = $classObject->currentUser;
                $this->currentUser = $classObject->currentUser;
            }

            // call the method
            $resultSet = !empty($loadCache) ? $loadCache : $classObject->{$method}();

            // check if the result set has a status code
            if(is_array($resultSet) && isset($resultSet['statusCode'])) {
                $this->statusCode = $resultSet['statusCode'];
                unset($resultSet['statusCode']);
            }

            if(!empty($loadCache)) {
                $this->cacheUse = true;
            }

            // set the must cache
            $validObject->cacheObject->allowCaching = $classObject->allowCaching;

            // call the method
            $finalResponse = is_array($resultSet) ? $resultSet : ['status' => 'success', 'message' => $resultSet];

            // set the cache
            if(empty($loadCache)) {
                $validObject->cacheObject->bypassCacheStatus = $classObject->bypassCacheStatus;
                $validObject->cacheObject->handle($class, $method, $classObject->incomingPayload, 'set', $finalResponse, $classObject->currentUser);
            }

            // if the invalidation list is not empty, invalidate the cache
            if(isset($finalResponse['invalidate']) && !empty($finalResponse['invalidate'])) {
                $cacheObject->invalidationList = $finalResponse['invalidate'];
                $cacheObject->invalidation();
            }

            // get the end time
            $end_time = date('Y-m-d H:i:s');

            foreach(['user_id', 'account_id', 'user_type', 'username', 'email'] as $key) {
                if(isset($finalResponse['data'][$key])) {
                    $classObject->payload[$key] = $finalResponse['data'][$key];
                }
            }

            // log the api request
            $cacheObject->logApiRequest($this->req_path, $this->requestMethod, $this->statusCode, $classObject->payload, $start_time, $end_time, $finalResponse);

            // return the final response
            return $finalResponse;

        } catch(\Exception $e) {
            $this->statusCode = 500;
            return Routing::error($e->getMessage());
        }

    }

}
