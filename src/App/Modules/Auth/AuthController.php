<?php

namespace App\Modules\Auth;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;
use App\Modules\Auth\Model\UserQuery;
use App\Modules\Auth\Model\ResetToken;
use App\Modules\Auth\Model\ResetTokenQuery;
use App\Modules\Auth\Services\UserRegistrationService;
use App\Modules\Auth\Exceptions\EmailAlreadyRegisteredException;
use App\Core\Services\Mail\SMTPService;
use Ramsey\Uuid\Uuid;

class AuthController
{
    function __construct(
        User $user,
        UserQuery $userQuery,
        SMTPService $smtp,
        UserRegistrationService $userRegService)
    {
        $this->user = $user;
        $this->userQuery = $userQuery;
        $this->smtp = $smtp;
        $this->userRegService = $userRegService;
    }

    public function register(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $checklist = ['email', 'password', 'confirmation_password'];
        if(!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 400);
        }

        $email = $params['email'];
        $password = $params['password'];
        $this->userRegService->register($email, $password);

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
        if(!$user) {
            return $response->withJson(['success' => true], 200);
        }

        $resetToken = ResetTokenQuery::create()->findOneByEmail($params['email']);
        if($resetToken) {
            $resetToken->delete();
        }

        $resetToken = new ResetToken();
        $resetToken->setEmail($params['email']);
        $resetToken->setToken($token);

        $expiredAt = new \DateTime();
        $expiredAt->add(new \DateInterval('PT1H'));
        $resetToken->setExpiredAt($expiredAt->format('Y-m-d H:i:s'));
        $resetToken->save();

        $this->smtp->addAddress($params['email']);
        $this->smtp->Body = 'your reset token is <b>' . $token . '</b>';
        if(!$this->smtp->send()) {
            //TODO: Define error code
            return $response->withJson([
                'success' => false,
                'error' => [
                    'code' => '0000',
                    'message' => $this->smtp->ErrorInfo
                ]
            ], 500);
        }

        return $response->withJson(['success' => true], 200);
    }

    public function resetPassword(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $checklist = ['reset_token', 'password', 'confirmation_password'];
        if(!$this->validateRequiredParam($checklist, $request)) {
             return $response->withJson(['success' => false], 400);
        }

        $resetToken = ResetTokenQuery::create()->findOneByToken($params['reset_token']);
        if(!$resetToken) {
            return $response->withJson([
                'success' => false,
                'message' => 'invalid reset token',
                'data' => null], 400);
        }

        $currentDate = new \DateTime();
        if($currentDate > $resetToken->getExpiredAt()) {
            $resetToken->delete();
            return $response->withJson([
                'success' => false,
                'message' => 'invalid reset token',
                'data' => null], 400);
        }

        $user = UserQuery::create()->findOneByEmail($resetToken->getEmail());

        $hashedPassword = password_hash($params['password'], PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);

        $user->setUpdatedAt($currentDate->format('Y-m-d H:i:s'));

        if($user->save()) {
            $resetToken->delete();
        }

        return $response->withJson([
            'success' => true,
            'message' => 'your password has been changed',
            'data' => null], 200);
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
