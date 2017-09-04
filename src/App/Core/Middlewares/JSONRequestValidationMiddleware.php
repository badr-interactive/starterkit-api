<?php

namespace App\Core\Middlewares;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class JSONRequestValidationMiddleware
{
    public function __invoke(
        Request $request,
        Response $response, $next)
    {
        $contentType = $request->getContentType();
        if($contentType !== 'application/json' ||
            $contentType !== 'application/json; charset=utf-8') {
            return $response->withJson(['success' => false], 400);
        }

        $response = $next($request, $response);
        return $response;
    }
}
