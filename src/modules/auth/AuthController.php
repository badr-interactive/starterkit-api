<?php

namespace App\Modules\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;
use Ramsey\Uuid\Uuid;

class AuthController
{
    public function register(Request $request, Response $response)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if($contentType !== 'application/json') {
            return $response->withJson(['success' => false], 400);
        }

        $params = $request->getParsedBody();
        $checklist = ['email', 'password', 'confirmation_password'];
        if(!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 400);
        }

        // TODO: Inject models using DI?
        $user = new User;
        $uuid = Uuid::uuid4()->toString();
        $hashedPassword = password_hash($params['password'], PASSWORD_BCRYPT);
        $user->setEmail($params['email']);
        $user->setPassword($hashedPassword);
        $user->setUuid($uuid);
        $user->setCreatedAt(date('Y-m-d H:i:s'));
        $user->save();

        $responseData = [
            'success' => true,
            'message' => 'user registration success',
            'data' => null
        ];

        return $response->withJson($responseData, 200);
    }

    public function login(Request $request, Response $response)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if($contentType !== 'application/json') {
            return $response->withJson(['success' => false], 400);
        }

        $params = $request->getParsedBody();
        $checklist = ['email', 'password', 'device_id', 'fcm_token'];
        if(!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 401);
        }
        return $response->withJson(['success' => true], 200);
    }

    private function validateRequiredParam($checklist = [], $request)
    {
        $params = $request->getParsedBody();
        if(!$params) {
            return false;
        }

        foreach($checklist as $check) {
            if(!array_key_exists($check, $params)) {
                return false;
            }
        }

        return true;
    }
}