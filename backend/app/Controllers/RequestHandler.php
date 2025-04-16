<?php

namespace App\Controllers;

use App\Libraries\Routing;
// use App\Models\UsersModel;
use Stripe\StripeClient;
use App\Models\DbTables;

class RequestHandler extends BaseController
{

    public $parsedPayload;
    public $cleanPayload;

    /**
     * @var bool
     */
    public $routingInfo = [];

    /**
     * Validate the request.
     *
     * @param string $class
     * @param string $method
     * @param string $requestMethod
     * @param array  $payload
     * @param object $classObject
     * @param array  $splitPath
     * 
     * @return bool
     */
    public function validateRequest($class, $method, $requestMethod, $payload = [], $classObject = null, $splitPath = [])
    {
        // get the class name
        $validationClass = '\\App\\Libraries\\Validation\\'.ucfirst($class).'Validation';

        // if the class does not exist, return true
        if (!class_exists($validationClass)) {
            return true;
        }

        // get the routes
        $routes = (new $validationClass())->routes ?? [];

        // set the routing info
        $this->routingInfo = $routes;

        // if the method does not exist, return true
        if (!isset($routes[$method])) {
            $skip = true;

            $splitValues = [];
            foreach($splitPath as $key => $value) {
                if(!in_array($value, ['api', $class, $method])) {
                    $splitValues[] = $value;
                }
            }

            $endQuery = false;

            // loop through the routes
            foreach($routes as $ikey => $value) {
                // if the key contains the method, split the key and add the next step to the payload
                if(strpos($ikey, $method) !== false) {

                    // split the key
                    $split = explode(":", $ikey);

                    // if the request method is not in the method, continue
                    if(!in_array($requestMethod, explode(',', $value['method']))) {
                        continue;
                    }

                    $routesUrl = "{$method}:";
                    foreach($split as $key => $value) {
                        if($key == 0) continue;
                        $payload[$value] = $splitValues[$key - 1] ?? ($payload[$value] ?? 0);
                        $routesUrl .= "{$value}:";
                    }
                    $routesUrl = rtrim($routesUrl, ":");

                    if(isset($routes[$routesUrl])) {
                        $endQuery = true;
                        $routes[$method] = $routes[$routesUrl];
                        $routes[$method]['payload'] = $routes[$routesUrl]['payload'];
                        unset($routes[$routesUrl]);
                    }

                    if($endQuery) {
                        break;
                    }
                }
            }
        }

        // if the method does not exist, return an error
        if(!isset($routes[$method])) {
            $this->statusCode = 405;
            return Routing::error('The route you are trying to access is unavailable.');
        }

        // get the route method
        $routeMethod = explode(',', $routes[$method]['method']);
        foreach ($routeMethod as $acceptedMethod) {
            $routesList[] = trim($acceptedMethod);
        }
        // validate the request method
        if (!in_array(strtoupper($requestMethod), $routesList)) {
            $this->statusCode = 405;
            return Routing::error('The route you are trying to access is unavailable.');
        }

        // validate the payload
        if (isset($routes[$method]['payload'])) {
            // loop through the payload
            foreach ($routes[$method]['payload'] as $key => $value) {
                // if the key is not in the payload, check if it is required
                if (!in_array($key, array_keys($payload))) {
                    if (strpos($value, 'required') !== false) {
                        $result[] = 'The variable '.$key.' is required.';
                    }
                }
            }

            // set the accepted payload
            $classObject->acceptedPayload = $routes[$method]['payload'];

            // validate the data
            if (!$this->validateData($payload, $routes[$method]['payload'])) {
                $result = $this->validator->getErrors();
            }

            // set the payload set
            foreach($classObject->acceptedPayload as $key => $value) {
                if(in_array($key, array_keys($payload))) {
                    $classObject->submittedPayload[$key] = $payload[$key];
                }
            }

            // if the result is not empty, return the error
            if (!empty($result)) {
                $this->statusCode = 400;

                return [
                    'status' => 'error',
                    'data' => $result,
                ];
            }
        }

        // get the should authenticate
        $shouldAuthenticate = $routes[$method]['authenticate'] ?? false;

        // set the request method in the class object
        $classObject->requestMethod = $requestMethod;

        // if the route should authenticate and the token is not in the payload, return an error
        if ($shouldAuthenticate) {
            // set the status code to 401
            $this->statusCode = 401;

            // if the token auth is in the payload, set the token
            if (in_array('token_auth', array_keys($payload)) && !in_array('token', array_keys($payload))) {
                $payload['token'] = $payload['token_auth'];
            }

            // if the user data is not set, return an error
            if (!in_array('token', array_keys($payload)) || empty($payload['token'])) {
                Routing::$must_login = true;
                return Routing::unauthorized();
            }
            
            // if the force invalidate is set, set the payload
            if(isset($routes[$method]['force_invalidate']) && $routes[$method]['force_invalidate']) {
                $payload['force_invalidate'] = true;
            }
            
            // perform a validation of the token here
            $authModel = (new \App\Controllers\Auth\Auth());
            $authModel->payload = $payload;
            $authModel->cacheObject = $this->cacheObject;
            $userToken = $authModel->validateToken($payload['token'], "{$class}/{$method}");
            
            // if the token is invalid, return an error
            if (empty($userToken)) {
                Routing::$must_login = true;
                return Routing::unauthorized('The token you provided is invalid.');
            }
            
            // if the authenticate user is not 1, return an error
            $forceAuth = getenv('AUTHENTICATE_USER');
            
            // if the route requires admin and the user is not an admin, return an error
            foreach (['isAdmin', 'isSuperAdmin'] as $key) {
                if (!empty($routes[$method][$key]) && !$userToken[$key] && $forceAuth) {
                    return Routing::denied('You do not have permission to access this resource.');
                }
            }

            // Forcefully set must login to false
            Routing::$must_login = false;

            // if the route requires site check and the site does not exist, return an error
            if (empty($userToken['isAdmin']) && $forceAuth) {
                // loop through the access groups
                foreach (['view', 'write', 'admin'] as $access) {
                    // check if the user has access to the site
                    if (!empty($routes[$method]["{$access}_access"])) {
                        // set the id site
                        $courseId = (int)($payload['courseId'] ?? ($payload['courseId'] ?? 0));
                        // if the user does not have access to the site, return an error
                        if (!empty($courseId) && isset($userToken['access_groups'][$access]) && !in_array((int) $courseId, $userToken['access_groups'][$access])) {
                            return Routing::denied("You do not have {$access} access to this course.");
                        }
                        // check if the user has access to the account
                        if(!empty($userToken['accounts_group']) && !empty($payload['account_id'])) {
                            if(!in_array($payload['account_id'], array_keys($userToken['accounts_group']))) {
                                return Routing::denied("You do not have {$access} access to this account.");
                            }
                            if(!in_array("{$access}_access", $userToken['accounts_group'][$payload['account_id']]['access'])) {
                                return Routing::denied("You do not have {$access} access to perform this action on this account.");
                            }
                        }
                    }
                }
            }

            // set the force auth
            $classObject->forceAuth = $forceAuth;

            try {
                // initialize the stripe object
                if(!empty(configs('stripe_test_key'))) {
                    $classObject->stripeObject = new StripeClient(configs('stripe_test_key'));
                }
            } catch (\Exception $e) {
                // set the stripe object to null
                $classObject->stripeObject = null;
            }

            // set the current user
            $classObject->currentUser = $userToken;
            $this->cacheObject->currentUser = $userToken;
            
            
            // set the access groups if the logged in user is not an admin
            if (!empty($authModel->accessGroups) && !$userToken['isAdmin']) {
                $classObject->accessGroups = $authModel->accessGroups;
                $classObject->siteAccessGrouping = $authModel->siteAccessGrouping;
            }
        }

        // convert the courseId, courseIdHsr, and deviceType to integers
        foreach(['deviceType', 'accountId', 'userId', 'courseId', 'categoryId', 'instructorId', 'studentId'] as $key) {
            if(isset($payload[$key])) {
                $payload[$key] = (int) $payload[$key];
            }
        }

        $classObject->dbTables = new DbTables();
        $classObject->routingInfo = $this->routingInfo;

        // set the request container this will be used for logging and debugging
        $classObject->logContainer = [
            'className' => $class,
            'classMethod' => $method,
            'payload' => $payload,
            'uniqueId' => $this->uniqueId,
            'mainRawId' => $this->mainRawId,
            'requestMethod' => $requestMethod
        ];

        // set the parsed payload
        $this->parsedPayload = $payload;
        $this->cleanPayload = $payload;

        // set the status code to 200
        $this->statusCode = 200;
        
        // return true
        return true;
    }

    /**
     * Manipulate the payload
     * 
     * @param array $payload
     * @param string $class
     * @param int $uniqueId
     * @param int $mainRawId
     * 
     * @return array
     */
    public function manipulatePayload($payload, $class, $uniqueId, $mainRawId) {

        // if the clean payload is not empty, set the clean payload
        $this->cleanPayload = !empty($this->cleanPayload) ? $this->cleanPayload : [];

        // if the class is heatmaps and the payload is an array, merge the payload with the clean payload
        if(in_array($class, ['heatmaps']) && is_array($payload)) {
            $payload = array_merge($payload, $this->cleanPayload);
        }

        return $payload;
    }
}
