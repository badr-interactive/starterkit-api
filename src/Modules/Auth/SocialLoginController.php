<?php

namespace App\Modules\Auth;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SocialLoginController
{
    function __construct(Container $container)
    {

    }

    public function login(Request $request, Response $response)
    {
        
    }
}