<?php 

namespace App\Libraries;

use Config\Encryption;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Encryption\Exceptions\EncryptionException;
use Exception;

class Caching {

    /**
     * Cache object
     * 
     * @var object
     */
    private $cache;

    /**
     * Database object
     * 
     * @var object
     */
    public $dbObject;

    /**
     * Api database object
     * 
     * @var object
     */
    public $apiDbObject;

    /**
     * Encrypt object
     * 
     * @var object
     */
    private $encryptObject;

    /**
     * Time to live
     * 
     * @var int
     */
    private $ttl = 1800;

    /**
     * Storage
     * 
     * Allow setting either sqlite or file as storage engine
     * 
     * @var string
     */
    private $storage = 'sqlite';

    /**
     * Is file storage
     * 
     * @var bool
     */
    private $fileStore = false;

    /**
     * Must cache
     * 
     * @var bool
     */
    public $allowCaching = true;

    /**
     * @var array
     */
    public $invalidationList = [];

    /**
     * Current user
     * 
     * @var array
     */
    public $currentUser = [];

    /**
     * Ignore timer
     * 
     * @var bool
     */
    public $ignoreTimer = false;

    /**
     * Account id
     * 
     * @var int
     */
    public $accountId = 0;

    /**
     * Sqlite loaded
     * 
     * @var bool
     */
    public $sqliteLoaded = true;

    /**
     * Bypass status
     * 
     * @var bool
     */
    public $bypassCacheStatus = false;

    /**
     * Constructor
     */
    public function __construct() {

        try {

            // if the db object is empty, connect to the cache database
            if(empty($this->dbObject)) {

                // get the cache object
                $this->cache = service('cache');

                // get the encryption object
                $config         = config(Encryption::class);
                $config->key    = 'heat_IencTest-Key_secret';
                $config->driver = 'OpenSSL';

                // set the sqlite loaded to true
                $this->sqliteLoaded = extension_loaded('sqlite3');

                // if the sqlite is loaded, use the sqlite database
                if($this->sqliteLoaded) {
                    $this->dbObject = db_connect('cache');
                    $this->apiDbObject = db_connect('apiCache');
                }
                // if the sqlite is not loaded, use the default database
                else {
                    $this->dbObject = db_connect('default');
                    $this->apiDbObject = db_connect('default');
                }

                // initialize the cache table
                if($this->sqliteLoaded) {
                    $this->initialize();
                }

                // initialize the encryption object
                $this->encryptObject = service('encrypter', $config);

            }

            // print_r($this->currentUser);

        } catch(Exception $e) { }
    }

    /**
     * Initialize the cache table
     * 
     * @return void
     */
    private function initialize() {

        try {

            // create the cache table
            $this->dbObject->query("CREATE TABLE IF NOT EXISTS cache (
                cache_key TEXT PRIMARY KEY,
                value TEXT,
                setup TEXT,
                account_id INTEGER DEFAULT 0,
                expires DATETIME,
                secondary_key TEXT NULL,
                server_time DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX IF NOT EXISTS cache_account_id_index ON cache (account_id);
            CREATE INDEX IF NOT EXISTS cache_key_index ON cache (cache_key);
            CREATE INDEX IF NOT EXISTS cache_setup_index ON cache (setup);");

            // create the events log table
            $this->dbObject->query("CREATE TABLE IF NOT EXISTS events_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                idsite INTEGER DEFAULT 0,
                user_id INTEGER DEFAULT 0,
                recorder INTEGER DEFAULT 0,
                account_id INTEGER DEFAULT 0,
                section TEXT,
                data TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX IF NOT EXISTS idx_idsite ON events_log (idsite);
            CREATE INDEX IF NOT EXISTS idx_user_id ON events_log (user_id);
            CREATE INDEX IF NOT EXISTS idx_account_id ON events_log (account_id);
            CREATE INDEX IF NOT EXISTS idx_recorder ON events_log (recorder);");

            // create the events log table
            $this->apiDbObject->query("CREATE TABLE IF NOT EXISTS api_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                idsite INTEGER DEFAULT 0,
                user_id INTEGER DEFAULT 0,
                account_id INTEGER DEFAULT 0,
                path TEXT,
                method TEXT,
                payload TEXT,
                response TEXT,
                ipaddress TEXT,
                agent TEXT,
                code INTEGER,
                start_time DATETIME,
                end_time DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX IF NOT EXISTS idx_idsite ON api_logs (idsite);
            CREATE INDEX IF NOT EXISTS idx_user_id ON api_logs (user_id);
            CREATE INDEX IF NOT EXISTS idx_account_id ON api_logs (account_id);
            CREATE INDEX IF NOT EXISTS idx_ipaddress ON api_logs (ipaddress);
            CREATE INDEX IF NOT EXISTS idx_code ON api_logs (code);
            CREATE INDEX IF NOT EXISTS idx_method ON api_logs (method);");

            // update some few connection settings
            setDatabaseSettings($this->dbObject);

        } catch(DatabaseException $e) {}

    }

    /**
     * Handle the cache
     * 
     * @param string $class
     * @param string $method
     * @param array $payload
     * @param string $request
     * @param array $response
     * 
     * @return void
     */
    public function handle($class, $method, $payload = [], $request = 'get', $response = [], $user = null) {

        $allowedCache = [
            'data' => [
                'methods' => [
                    'clicks'
                ],
                'ttl' => 1800
            ],
            'auth' => [
                'methods' => [
                    'validateToken',
                    'profile'
                ],
                'invalid'=>[
                    'logout'=>['validateToken'],
                ],
                'ttl' => 1800
            ],
            'subscription' => [
                "invalid" => [
                    'cancel' => ['list'],
                    'upgrade' => ['list'],
                    'reactivate' => ['list']
                ],
                'ttl' => 3600
            ],
            'dashboard' => [
                'methods' => [
                    'insight', 
                    'summary', 
                    'analytics', 
                    'populate'
                ],
                'ttl' => configs('heatmaps_ttl')
            ],
            'users' => [
                'methods' => [
                    // 'list',
                ],
                "invalid" => [
                    'create' => ['list'],
                    'update' => ['list'],
                    
                ],
                'ttl' => 3600
            ]
        ];

        // if the payload has do_not_cache or debug, return false
        if(!empty($payload) && (
            in_array('do_not_cache', array_keys($payload)) || 
            in_array('debugger', array_keys($payload)) && $request == 'get'
        )) return false;
        
        // if the class or method is not allowed, return false
        if((!isset($allowedCache[$class]['methods']) || !in_array($method, $allowedCache[$class]['methods']))) {
            return false;
        }

        // if the method is insight or summary and the idSite is set, check if the website is created
        if((in_array($method, ['insight', 'summary']) || (in_array($method, ['list']) && in_array($class, ['recordings']))) && isset($payload['idSite'])) {            
            $checkCreatedAt = $this->get(create_cache_key('website', 'creation', ['idSite' => $payload['idSite']]));
            if(!empty($checkCreatedAt)) return false;
        }

        // if the payload has ipaddress or agent, unset it
        foreach($payload as $key => $value) {
            if(in_array($key, ['ipaddress', 'agent', 'force_invalidate', 'unknown', 'oauth_token', 'oauth_verifier'])) {
                if(isset($payload[$key])) unset($payload[$key]);
            }
            // if the payload has account_id, user_id, idsite or idSite, set it to the integer value
            if(in_array($key, ['account_id', 'user_id', 'idsite', 'idSite'])) {
                if(!empty($payload[$key])) {
                    $payload[$key] = (int)$payload[$key];
                }
            }
        }

        // if the payload has token, unset it
        if(!empty($payload) && isset($payload['token'])) {
            if(!empty($payload) && count($payload) == 1 && array_key_first($payload) == 'token') {
            } else {
                unset($payload['token']);
            }
        }

        // if the account id is empty, set it to the current user account id
        if(empty($this->accountId) && !empty($this->currentUser)) {
            $this->accountId = $this->currentUser['account_id'];
        }

        // if the db object is empty, return false
        if(empty($this->dbObject)) return false;

        // set the ttl
        $this->ttl = $allowedCache[$class]['ttl'] ?? $this->ttl;

        // set the storage
        $this->storage = $allowedCache[$class]['storage'] ?? $this->storage;

        // set the storage
        $this->fileStore = (bool) ($this->storage == 'file');

        // get the cache key
        $cacheKey = create_cache_key($class, $method, $payload);

        // if the class has invalidation list, invalidate the cache
        if(isset($allowedCache[$class]['invalid']) ) {
            if (in_array($method, array_keys($allowedCache[$class]['invalid']))){
                foreach ($allowedCache[$class]['invalid'] as $key) {
                    $secondary_key = $this->generateSecondaryKey($class, $method, $payload, $user);
                    $this->invalidate('secondary_key', $secondary_key);
                }
                return;
            }
        }

        if($class == 'data' && $method == 'revenue') {
            print_r($payload);
            print $class . "\n";
            print $method . "\n";
        }
        
        // get the secondary key
        $secondary_key = $this->generateSecondaryKey($class, $method, $payload, $user);
        
        // if the request is get, return the cache
        if($request == 'get') {

            // get the cache
            return $this->get($cacheKey, $class, $method);

            // if the request is set, set the cache
        } elseif($request == 'set' && !empty($response)) {

            // if the response does not have access_groups or account_ids, check the status
            if(!$this->bypassCacheStatus) {
                if(!isset($response['access_groups']) && !isset($response['account_ids']) && !isset($response['token'])) {
                    // if the status is not success, return false
                    if(!isset($response['status']) ||isset($response['status']) && ($response['status'] !== 'success')) {
                        return false;
                    }
                    // if the data is empty, return false
                    if(empty($response['data'])) return false;
                }
            }

            // if the class is dashboard and the method is summary, set the cache
            if($class == 'dashboard' && $method == 'summary') {
                $this->save(md5($cacheKey."extended"), $response, "{$class}.{$method}", $secondary_key, (60 * 60 * 24));
            }
            
            // set the cache
            $this->save($cacheKey, $response, "{$class}.{$method}", $secondary_key);

            // if the request is delete, delete the cache
        } elseif($request == 'delete') {
            $this->delete($cacheKey);

            // if the request is clear, clear the cache
        } elseif($request == 'clean') {
            $this->clean();
        }

        return false;
    }

    /**
     * Get the cache
     *
     * @param $class
     * @param $method
     * @param $payload
     * @param $user
     * @return mixed
     */
    public function generateSecondaryKey($class, $method, $payload, $user): string
    {
        
        try {
            // get the account id
            $account = $user == null ? '': ($user['account_id'] ?? '');
            
            // get the site id
            $siteId = $payload == null ? '': $payload['idSite'] ?? '';
            
            // get the token
            $token = $payload == null ? '': $payload['token'] ?? '';
            
            // get the key
            $key = "{$class}.{$method}";
            
            if ( $account != '' ){
                $key .= ".{$account}" ;
            } elseif ( $siteId !== '') {
                $key .=  ".{$siteId}" ;
            } elseif ( $token !== '') {
                $key .=  ".{$token}" ;
            } 
            return $key;
        }
        catch (\Exception $e) {
            return "{$class}.{$method}";
        }
    }

    /**
     * Get the cache
     * 
     * @param string $key
     * @param string $class
     * @param string $method
     * @return mixed
     */
    public function get($key,  $class = '', $method = '') {

        try {

            // get the cache
            $cache = $this->fileStore ? $this->cache->get($key) : $this->dbObject->query("SELECT * FROM cache WHERE cache_key = '{$key}'")->getRowArray();

            // if the cache is not empty, return the cache
            if(!empty($cache)) {

                // if the cache is expired, delete the cache
                if(!$this->ignoreTimer && !$this->fileStore && strtotime($cache['expires']) < strtotime(date('Y-m-d H:i:s'))) {
                    $this->delete($key);
                    return false;
                }

                try {
                    // if the storage is file, use the cache content
                    $cacheContent = $this->fileStore ? $cache : $cache['value'];
                    // decrypt the cache
                    $decrypted = $this->encryptObject->decrypt(base64_decode($cacheContent));
                } catch(EncryptionException $e) {
                    $this->delete($key);
                    return false;
                }

                if(!empty($class) && $class == 'whole_data') {
                    $cache['value'] = json_decode($decrypted, true);
                    return $cache;
                }

                // if the cache is not expired, return the cache
                return json_decode($decrypted, true);
            }

            return false;
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the cache
     * 
     * @param string $table
     * @param array $data
     * @param array $whereIn
     * @param string $column
     * 
     * @return mixed
     */
    public function list($table, $data = [], $whereIn = [], $column = 'section') {
        try {

            // get the cache
            $query = $this->dbObject->table($table)
                                ->select("idsite, user_id, account_id, section, data")
                                ->where($data);

            // if the whereIn is not empty, add it to the query
            if(!empty($whereIn)) {
                $query->whereIn($column, $whereIn);
            }

            return $query->orderBy('created_at', 'DESC')->get()->getResultArray();

        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Remove the cache
     * 
     * @param string $table
     * @param array $data
     * 
     * @return mixed
     */
    public function remove($table, $data = []) {
        try {
            // get the cache
            return $this->dbObject->table($table)->where($data)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create an event
     * 
     * @param string $table
     * @param array $data
     * 
     * @return mixed
     */
    public function create($table, $data) {
        try {
            // get the cache
            $this->dbObject->table($table)->insert($data);
            return $this->dbObject->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Set the cache
     * 
     * @param string $key
     * @param mixed $value
     * @param string $setup
     * 
     * @return mixed
     */
    public function save($key, $value, $setup, $secondary_key = null, $ttl = null) {
        try {
            // if the must cache is false, return false
            if(!$this->allowCaching) return false;

            // encrypt the response
            $encryptData = $this->encryptObject->encrypt(json_encode($value));

            // set the ttl
            $ttl = !empty($ttl) ? $ttl : $this->ttl;

            // if the storage is file, use the cache object
            if($this->fileStore) {
                return $this->cache->save($key, base64_encode($encryptData), $ttl);
            }

            // set the expiry date
            $expiry = date('Y-m-d H:i:s', strtotime("+{$ttl} seconds"));

            // check if the cache already exists
            $checkCache = $this->dbObject->query("SELECT * FROM cache WHERE cache_key = '{$key}'")->getRowArray();
            if(!empty($checkCache)) {
                // update the cache
                return $this->dbObject->query("UPDATE cache SET value = ?, expires = '{$expiry}' WHERE cache_key = '{$key}'", [base64_encode($encryptData)]);
            }
    
            // insert the cache
            $this->dbObject->table('cache')->insert([
                'cache_key' => $key,
                'value' => base64_encode($encryptData),
                'setup' => $setup,
                'secondary_key' => $secondary_key,
                'account_id' => $this->accountId,
                'expires' => $expiry,
                'server_time' => date('Y-m-d H:i:s')
            ]);

        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete the cache
     * 
     * @param string $key
     * @param string $setup
     * 
     * @return mixed
     */
    public function delete($key = null, $setup = null, $secondary_key = null, $account_id = null): mixed
    {
        try {
            // if the storage is file, use the cache object
            if (!empty($key)) {
                $this->cache->delete($key);
            }

            // set the where clause
            if($key) $whereClause = ['cache_key' => $key];
            
            // if the setup is not empty, add it to the where clause
            if($setup) $whereClause['setup'] = $setup;
            if($secondary_key) $whereClause['secondary_key'] = $secondary_key;
            if($account_id) $whereClause['account_id'] = $account_id;

            // if the where clause is empty, return false
            if(empty($whereClause)) return false;

            // delete the cache
            return $this->dbObject->table('cache')->where($whereClause)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }
    
    /**
     * Delete the cache
     *
     * @param string $by
     * @param string $keys
     *
     * @return mixed
     */
    public function invalidate(string $by, string $key):void {
        switch ($by) {
            case ('setup'):
                $this->delete(null, $key);
                break;
            case ('secondary_key'):
                $this->delete(null, null, $key );
                break;
            case 'custom':
                break;
            default:
                $this->delete($key);
                break;
        }
    }

    /**
     * Invalidate the cache
     * 
     * @return mixed
     */
    public function invalidation() {
        $loopData = $this->invalidationList['direct'] ?? $this->invalidationList;
        foreach($loopData as $key => $value) {
            if(is_array($value)) {
                foreach($value as $method => $payload) {
                    $this->delete(create_cache_key($key, $method, $payload));
                }
            } else {
                $this->delete(md5($key . '_' . $value));
            }
        }
        if(!empty($this->invalidationList['group'])) {
            foreach($this->invalidationList['group'] as $key => $whereClause) {
                try {
                    if(!is_array($whereClause)) continue;
                    return $this->dbObject->table('cache')->where($whereClause)->delete();
                } catch(DatabaseException $e) {}
            }
        }
    }

    /**
     * Clear the cache
     * 
     * @return mixed
     */
    public function clean() {
        try {
            // if the storage is file, use the cache object
            return $this->cache->clean();
            // clear the cache
            return $this->dbObject->table('cache')->truncate();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Log the api request
     * 
     * @param string $path
     * @param string $method
     * @param int $code
     * @param array $payload
     * @param array $response
     * @return void
     */
    public function logApiRequest($path, $method, $code, $payload, $start_time, $end_time, $response = []) {

        try {

            // if the payload has ipaddress or agent, unset it
            foreach($payload as $key => $value) {
                if(in_array($key, ['force_invalidate', 'unknown'])) {
                    if(isset($payload[$key])) unset($payload[$key]);
                }
            }

            // if the payload has token then replace the last 6 characters with *
            foreach(['token', 'password', 'password_confirm', 'secret'] as $key) {
                if(isset($payload[$key])) {
                    $payload[$key] = html_entity_decode($payload[$key]);
                    $payload[$key] = '[HIDDEN]';
                }
            }

            // set the ipaddress and agent
            $data['ipaddress'] = $payload['ipaddress'] ?? '';
            $data['agent'] = $payload['agent'] ?? '';

            // unset the ipaddress and agent
            unset($payload['ipaddress']);
            unset($payload['agent']);

            // set the data
            $data['path'] = $path;
            $data['method'] = $method;
            $data['code'] = $code;
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $data['payload'] = json_encode($payload);

            // if the path is not in the array, return true
            if(in_array($path, ['api/events/apilogs', 'api/events/create', 'api/access/load', 'api/data/query', 
                'api/auth/confirm', 'api/heatmaps/configs', 'api/tracking/ip'])) {
                return true;
            }

            if(!empty($payload['user_id'])) $data['user_id'] = $payload['user_id'];
            if(!empty($payload['account_id'])) $data['account_id'] = $payload['account_id'];

            // set the user id and account id
            if(!empty($this->currentUser['user_id'])) $data['user_id'] = $this->currentUser['user_id'];
            if(!empty($payload['idsite'])) $data['idsite'] = $payload['idsite'];
            if(!empty($payload['idSite'])) $data['idsite'] = $payload['idSite'];
            if(!empty($this->currentUser['account_id'])) $data['account_id'] = $this->currentUser['account_id'];

            $data['created_at'] = date('Y-m-d H:i:s');

            // insert the data
            $this->apiDbObject->table('api_logs')->insert($data);

        } catch(Exception $e) {}
        
    }

    /**
     * Get the api logs
     * 
     * @param array $bind
     * @param int $limit
     * @param int $offset
     * 
     * @return mixed
     */
    public function getApiLogs($bind = [], $limit = 1000, $offset = 0) {
        try {
            return $this->apiDbObject
                        ->table('api_logs')
                        ->select('idsite, user_id, account_id, path, method, code, start_time, end_time, payload, agent')
                        ->where($bind)
                        ->limit($limit, $offset)
                        ->orderBy('created_at', 'DESC')
                        ->get()->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

}
