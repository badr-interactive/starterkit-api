<?php
date_default_timezone_set('Asia/Jakarta');
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$app = new \App\Core\FreedomApp();
$app->add(new \App\Core\Middlewares\JSONRequestValidationMiddleware);
$app->add(new \App\Core\Middlewares\CORSHandlerMiddleware);

// Run app
$app->run();
