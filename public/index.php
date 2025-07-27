<?php

// Start session
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set default timezone
date_default_timezone_set('America/Mexico_City');

// Define paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Autoload core classes
function autoloadCore($className) {
    $file = APP_PATH . '/core/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('autoloadCore');

// Load database connection
require_once APP_PATH . '/core/Database.php';

// Load router
require_once APP_PATH . '/core/Router.php';

try {
    // Create router instance
    $router = new Router();

    // Define routes
    $router->get('/', function() {
        header('Location: /demo/register');
        exit();
    });

    // Demo routes (no database required)
    $router->get('/demo/register', 'DemoController@showRegister');
    $router->post('/demo/register', 'DemoController@register');
    $router->get('/demo/success', 'DemoController@success');
    $router->post('/demo/validate-email', 'DemoController@validateEmail');

    // Production routes (require database)
    $router->get('/users/register', 'UserController@showRegister');
    $router->post('/users/register', 'UserController@register');
    $router->post('/users/validate-email', 'UserController@validateEmail');
    $router->get('/users', 'UserController@index');
    $router->get('/users/{id}', 'UserController@show');

    // Dispatch the request
    $router->dispatch();

} catch (Exception $e) {
    // Error handling
    http_response_code(500);
    if (ini_get('display_errors')) {
        echo "<h1>Error 500 - Internal Server Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<h1>Error 500 - Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }
}