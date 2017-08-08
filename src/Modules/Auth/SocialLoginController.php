<?php

namespace App\Modules\Auth;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;

class SocialLoginController
{
    function __construct(User $user)
    {

    }

    public function login(Request $request, Response $response)
    {
        
    }
}