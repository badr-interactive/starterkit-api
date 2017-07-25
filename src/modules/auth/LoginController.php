<?php

namespace App\Modules\Auth;
use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;
use Lcobucci\JWT\Builder;

class LoginController
{
    function __construct(Container $container)
    {
        if ($container->has('UserQuery')) {
            $this->userModel = $container->get('UserQuery');
        }
    }

    public function login(Request $request, Response $response)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if($contentType !== 'application/json') {
            return $response->withJson(['success' => false], 400);
        }

        $checklist = ['email', 'password'];
        if (!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 400);
        }

        $params = $request->getParsedBody();
        if (!$user = $this->validateUser($params)) {
            return $response->withJson(['success' => false], 400);            
        }
        
        $salt = $this->random();
        $token = $this->getToken($request, $salt);
        if (!$this->saveSalt($user, $salt)) {
            return $response->withJson(['success' => false], 400);            
        }

        $responseData = [
            "success" => true,
            "message" => "you are successfully logged in",
            "data" => [
                "id" => $user->getUuid(),
                "name" => $user->getEmail(),
                "photo" => "https://randomuser.me/api/portraits/men/21.jpg",
                "email" => $user->getEmail(),
                "access_token" => "Bearer ".$token,
            ]
        ];

        return $response->withJson($responseData, 200);
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

    private function validateUser($params)
    {
        $email = $params['email'];
        $user = $this->userModel
                    ->create()
                    ->filterByEmail($email)
                    ;

        if ($user->count() < 1) {
            return false;
        }

        $password = password_verify($params['password'], $user->findOne()->getPassword());
        if (!$password) {
            return false;
        }
        $user = $user->findOne();

        return $user;
    }

    private function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    private function getToken($request, $salt)
    {
        $token = (new Builder())->setIssuer($request->getUri()) // Configures the issuer (iss claim)
                        ->setAudience($request->getUri()) // Configures the audience (aud claim)
                        ->setId($salt, true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
                        ->set('uid', $this->random()) // Configures a new claim, called "uid"
                        ->getToken(); // Retrieves the generated token

        return $token;
    }

    private function saveSalt($user, $salt)
    {
        $user->setApiToken($salt);
        if (!$user->save()) {
            return false;
        }

        return true;
    }

}
