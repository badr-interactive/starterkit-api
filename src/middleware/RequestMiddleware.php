<?php

namespace App\Middleware\RequestMiddleware;

$app = new \Slim\App();

$app->add(function($request, $response, $next) {
    $contentType = $request->getHeaderLine('Content-Type');
    if($contentType !== 'application/json') {
        return $response->withJson(['success' => false, 'message' => 'Request format not supported.'], 400);
    }
    
    $response = $next($request, $response);
    
    return $response;
});

$app->run();
