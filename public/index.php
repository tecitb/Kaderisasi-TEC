<?php
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization");
header("X-Env-Hostname: ".gethostname());

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

define("BASE_URL", getenv("BASE_URL") ?: "http://localhost");

require __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Add default HTTP headers
/*$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', "GET, POST, OPTIONS, PUT, DELETE")
        ->withHeader('Access-Control-Allow-Headers', 'Authorization');
});*/

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
