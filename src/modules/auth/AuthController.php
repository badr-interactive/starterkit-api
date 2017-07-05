<?php

namespace App\Modules\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthController
{
    public function register(Request $request, Response $response)
    {
        return $response->withJson(['success' => true], 200);
    }
}