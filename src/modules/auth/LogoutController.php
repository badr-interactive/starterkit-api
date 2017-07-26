<?php

namespace App\Modules\Auth;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;

class LogoutController
{
    function __construct(Container $container)
    {
        if ($container->has('UserQuery')) {
            $this->userModel = $container->get('UserQuery');
        }

        if ($container->has('auth')) {
            $this->auth = $container->get('auth');
        }
    }

    public function logout(Request $request, Response $response)
    {
        $authorization = $request->getHeaderLine('Authorization');
        if(!$authorization) {
            return $response->withJson(['success' => false], 400);
        }

        try {
            $user = $this->auth;
            $user->setApiToken(null);
            $user->save();

        } catch (Exception $e) {
            return $response->withJson(['success' => false], 400);
        }

        $responseData = [
            "success" => true,
            "message" => "you are successfully logged out",
            "data" => null
        ];

        return $response->withJson($responseData, 200);
    }
}
