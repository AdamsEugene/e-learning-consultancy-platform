<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{

    /**
     * @var array
     */
    public $payload;

    /**
     * @var array
     */
    public $userData;

    /**
     * @var string
     */
    public $uniqueId;

    /**
     * @var string
     */
    public $mainRawId;

    /**
     * @var array
     */
    public $permissions;

    /**
     * @var array
     */
    public $accountsList;

    /**
     * @var string
     */
    public $requestMethod;

    /**
     * @var array
     */
    public $logContainer;

    /**
     * @var bool
     */
    public $forceAuth = true;

    /**
     * @var array
     */
    public $currentUser = [];

    /**
     * @var int
     */
    public $statusCode = 200;

    /**
     * @var array
     */
    public $accessGroups = [];

    /**
     * @var array
     */
    public $siteAccessGrouping = [];

    /**
     * @var int
     */
    public $defaultLimit = 100;

    /**
     * @var int
     */
    public $defaultOffset = 0;

    /**
     * @var bool
     */
    public $routingInfo = [];

    /**
     * @var array
     */
    public $acceptedPayload = [];

    /**
     * @var array
     */
    public $submittedPayload = [];

    /**
     * @var array
     */
    public $incomingPayload = [];

    /**
     * @var int
     */
    public $couponMaxUsage = 100;

    /**
     * @var array
     */
    protected $excludeNewUserTypes = ['Agency', 'Admin', 'Support', 'Marketing'];

    /**
     * @var StripeClient
     */
    public $stripeObject;

    /**
     * @var Caching
     */
    public $cacheObject;

    /**
     * @var bool
     */
    public $allowCaching = true;

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [
       'database', 'configs', 'text', 'auth', 'utilities', 'users', 'courses'
    ];

    /**
     * @var array
     */
    protected $dbTables;

    /**
     * @var array
     */
    protected $invalidationList = [];

    /**
     * Bypass status
     * 
     * @var bool
     */
    public $bypassCacheStatus = false;

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
    }
}