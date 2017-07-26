<?php

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

/**
* Process the application given a request method and URI
*
* @param string $requestMethod the request method (e.g. GET, POST, etc.)
* @param string $requestUri the request URI
* @param array|object|null $requestData the request data
* @return \Slim\Http\Response
*/
function runApp($requestMethod, $requestUri, $requestData = null, $dependencies = [])
{
    $withMiddleware = true;

    // Create a mock environment for testing with
    $environment = Environment::mock(
        [
            'REQUEST_METHOD' => $requestMethod,
            'REQUEST_URI' => $requestUri,
            'CONTENT_TYPE' => 'application/json'
        ]
    );

    // Set up a request object based on the environment
    $request = Request::createFromEnvironment($environment);

    // Add request data, if it exists
    if (isset($requestData)) {
        $body = $request->getBody();
        $body->write($requestData);
        $request = $request->withBody($body);
    }

    // Set up a response object
    $response = new Response();

    // Use the application settings
    $settings = require __DIR__ . '/../src/settings.php';
    
    // Instantiate the application
    $app = new App($settings);

    // Set up dependencies
    require __DIR__ . '/../src/dependencies.php';

    // Register additional dependencies
    if (!empty($dependencies)) {
        $container = $app->getContainer();
        foreach($dependencies as $key => $value) {
            if(is_callable($value)) {
                $container[$key] = $value;
            }
        }
    }

    // Register middleware
    if ($withMiddleware) {
        require __DIR__ . '/../src/middleware.php';
    }

    // Register routes
    require __DIR__ . '/../src/routes.php';

    // Propel configuration
    require __DIR__ . '/../src/connection.php';

    // Process the application
    $response = $app->process($request, $response);

    // Return the response
    return $response;
}