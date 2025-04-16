<?php 

namespace App\Libraries;

class Routing {

    public static $must_login = false;
    public static $httpCode = 200;
    public static $invalidationList = [];
    public static $pagination = [];
    /**
     * Invalidation list
     * 
     * @param array $data
     * 
     * @return void
     */
    public static function invalidation($data) {
        self::$invalidationList = $data;
    }

    /**
     * Denied
     * 
     * @return array
     */
    public static function denied($data = 'You are not authorized to perform the action on this resource.') {
        $response = [
            'status' => 'error',
            'data' => $data,
            'statusCode' => 403,
            'must_login' => self::$must_login,
        ];
        return $response;
    }

    /**
     * Denied
     * 
     * @return array
     */
    public static function unauthorized($data = 'You are not authorized to access this resource.', $message = []) {
        $response = [
            'status' => 'error',
            'data' => $data,
            'statusCode' => 200,
            'must_login' => true,
        ];
        if(!empty($message)) {
            $response['message'] = $message;
        }
        return $response;
    }

    /**
     * Error
     * 
     * @param string $message
     * @return array
     */
    public static function error($message, $record = [], $statusCode = 400) {
        return [
            'status' => $statusCode == 400 ? 'error' : 'success',
            'data' => $message,
            'record' => $record,
            'statusCode' => $statusCode
        ];
    }

    /**
     * Success
     * 
     * @param array $data
     * @return array
     */
    public static function success($data = [], $message = 'success', $statusCode = 'success') {
        return [
            'status' => $statusCode,
            'data' => $data,
            'message' => $message,
            'statusCode' => 200,
            'invalidate' => self::$invalidationList,
            'pagination' => self::$pagination
        ];
    }

    /**
     * Not found
     * 
     * @param string $resource
     * 
     * @return array
     */
    public static function notFound($resource = 'Resource', $useInfo = false) {
        return [
            'status' => 'error',
            'data' => $useInfo ? $resource : "{$resource} not found",
            'statusCode' => 404
        ];
    }

    /**
     * Created
     * 
     * @param array $data
     * @return array
     */
    public static function created($data = []) {
        return [
            'status' => 'success',
            'data' => $data['data'] ?? $data,
            'record' => $data['record'] ?? [],
            'statusCode' => 201,
            'invalidate' => self::$invalidationList
        ];
    }

    /**
     * Deleted
     * 
     * @return array
     */
    public static function deleted() {
        return [
            'status' => 'success',
            'data' => 'Resource deleted successfully.',
            'statusCode' => 200,
            'invalidate' => self::$invalidationList
        ];
    }

    /**
     * Updated
     * 
     * @param array $data
     * @return array
     */
    public static function updated($data = [], $record = []) {
        return [
            'status' => 'success',
            'data' => $data,
            'record' => $record,
            'statusCode' => 200,
            'invalidate' => self::$invalidationList
        ];
    }

    /**
     * Make the request
     * 
     * @param array $payload
     * @param string $path
     * 
     * @return object
     */
    public static function curlRequest($payload, $path = 'lambda', $method = 'POST') {

        // set the path url
        $pathUrl = [
            'insight' => getenv('PAGE_SPEED_URL'),
            'lambda' => getenv('LAMBDA_URL'),
            'ads' => getenv('ADS_URL'),
            'query'     => 'https://srapi.heatmapcore.com/query/$idVisit',
            'funnel' => 'https://z546sl2nyl.execute-api.us-west-2.amazonaws.com/Prod/api/v1/funnels',
            'funnel_predefined' => 'https://z546sl2nyl.execute-api.us-west-2.amazonaws.com/Prod/api/v1/funnels/predefined'
        ];
        // convert payload to params
        if ($method == 'GET') {
            if (!empty($payload) && is_array($payload)) {
                $params = http_build_query($payload, '', '&');
                $pathUrl[$path] .= '?' . $params;
            } elseif (!empty($payload) && is_string($payload)) {
                $pathUrl[$path] .= '?' . $payload;
            }
        }
        // set the period to month if not set
        if(empty($payload['period'])) {
            $payload['period'] = 'month';
        }
        // set the date to month if not set
        if(!empty($payload['date']) && in_array($payload['date'], ['today', 'month'])) {
            $payload['date'] = date('Y-m-d');
            $payload['period'] = $payload['date'] == 'today' ? 'day' : 'month';
        }
        // get the client
        $client = service('curlrequest');

        if(strpos($pathUrl[$path], '$') !== false) {
            foreach($payload as $key => $value) {
                $pathUrl[$path] = str_ireplace($key, $value, $pathUrl[$path]);
            }
        }

        // make the request
        if ($method == 'POST') {
            $response = $client->request($method, $pathUrl[$path] , [
                'json' => $payload
            ]);
        } elseif($method == 'GET') {
            $response = $client->request($method, $pathUrl[$path]);
        }
        
        // decode the response
        $response = json_decode($response->getBody(), true);

        // return the response
        return !empty($response) ? $response : [];

    }

    /**
     * Playback request
     * 
     * @param array $payload
     * @param string $path
     * 
     * @return object
     */
    public static function playbackRequest($payload, $path = 'lambda') {

        $cleanPayload = [];
        foreach($payload as $key => $item) {
            if(in_array($key, ['ipaddress', 'token', 'agent'])) {
                continue;
            }
            $cleanPayload[$key] = $item;
        }

        // set the path url
        $pathUrl = [
            // recording playback urls
            'setup'     => 'https://pb.heatmap.com/playback/setup',
            'action'    => 'https://pb.heatmap.com/playback/action',
            'images'    => 'https://pb.heatmap.com/playback/get-playback-images',
            'delete'    => 'https://pb.heatmap.com/playback/delete-from-s3',
            'mutation'  => 'https://pb.heatmap.com/playback/fix-missing-initial-mutation'
        ];

        // Initialize cURL
        $ch = curl_init($pathUrl[$path]);

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($cleanPayload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($cleanPayload))
            ]
        ]);

        // Execute cURL request
        $response = curl_exec($ch);

        // get the curl info
        $curlInfo = curl_getinfo($ch);

        // set the http code
        self::$httpCode = $curlInfo['http_code'];

        // Close cURL
        curl_close($ch);

        return !is_array($response) ? json_decode($response, true) : $response;
    }

}
?>
