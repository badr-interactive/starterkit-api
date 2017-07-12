<?php

namespace App\Modules\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthController
{
    public function register(Request $request, Response $response)
    {
        /*$contentType = $request->getHeaderLine('Content-Type');
        if($contentType !== 'application/json') {
            return $response->withJson(['success' => false], 400);
        }*/

        $request->getParam('email');
        return $response->withJson(['success' => true], 200);
    }
}