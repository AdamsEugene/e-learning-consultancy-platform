<?php

use CodeIgniter\Router\RouteCollection;

// $routes->setAutoRoute(true);

/**
 * @var RouteCollection $routes
 */
$routes->get("/", "Home::index");
$routes->set404Override("\App\Controllers\BaseRoute::control");

use App\Controllers\Api;

// request to api routing
$routes->get("/api", [Api::class, "index"]);

// request to api routing
$routes->match(["PUT", "DELETE", "GET", "POST", "OPTIONS"], "/api(:any)", [Api::class, "index/$1/$2/$3"]);

// crontab requests
$routes->cli('userjourney/(:segment)', 'Dashboard\Dashboard::journey/$1');