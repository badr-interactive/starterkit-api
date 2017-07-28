<?php

namespace App\Modules\Auth;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;
use App\Modules\Auth\Model\UserQuery;
use App\Modules\Auth\Model\ResetToken;
use App\Modules\Auth\Model\ResetTokenQuery;
use Ramsey\Uuid\Uuid;

class AuthController
{
    function __construct(Container $container)
    {
        if($container->has('User')) {
            $this->user = $container->get('User');
        }

        if($container->has('SMTPService')) {
            $this->smtp = $container->get('SMTPService');
        }
    }

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
        $uuid = Uuid::uuid4()->toString();
        $hashedPassword = password_hash($params['password'], PASSWORD_BCRYPT);
        $this->user->setEmail($params['email']);
        $this->user->setPassword($hashedPassword);
        $this->user->setUuid($uuid);
        $this->user->setCreatedAt(date('Y-m-d H:i:s'));
        $this->user->save();

        $responseData = [
            'success' => true,
            'message' => 'user registration success',
            'data' => null
        ];

        return $response->withJson($responseData, 200);
    }

    public function forgotPassword(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $checklist = ['email'];
        if(!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 400);
        }

        $token = strtoupper(substr(uniqid(), -6));
        $user = UserQuery::create()->findOneByEmail($params['email']);
        
        $resetToken = ResetTokenQuery::create()->findOneByEmail($params['email']);
        if($resetToken) {
            $resetToken->delete();
        }

        $resetToken = new ResetToken();
        $resetToken->setEmail($params['email']);
        $resetToken->setToken($token);
        $resetToken->setExpiredAt(date('Y-m-d H:i:s'));
        $resetToken->save();
        
        $this->smtp->addAddress($params['email']);
        $this->smtp->Body = 'your reset token is <b>' . $token . '</b>';
        if(!$this->smtp->send()) {
            return $response->withJson(['success' => false], 500);
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