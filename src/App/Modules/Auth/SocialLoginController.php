<?php

namespace App\Modules\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use DI\Container;
use Ramsey\Uuid\Uuid;
use App\Modules\Auth\Model\User;
use App\Modules\Auth\Model\UserQuery;
use App\Modules\Auth\Services\RegistrationService;
use App\Modules\Auth\Exceptions\InvalidTokenException;

class SocialLoginController
{
    function __construct(
        Container $container,
        User $user,
        UserQuery $userQuery,
        RegistrationService $userRegService)
    {
        $this->container = $container;
        $this->user = $user;
        $this->userQuery = $userQuery;
        $this->userRegService = $userRegService;
    }

    public function login(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $checklist = ['provider', 'token'];

        if(!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 400);
        }

        try {
            $provider = $params['provider'];
            $token = $params['token'];
            if($provider === 'google') {
                $responseData = $this->handleGoogleLogin($token);
                return $response->withJson($responseData, 200);
            } else if($provider === 'facebook') {
                $responseData = $this->handleFacebookLogin($token);
                return $response->withJson($responseData, 200);
            }
        } catch (\App\Modules\Auth\Exceptions\InvalidTokenException $e) {
            return $response->withJson(['success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()]], 401);
        } catch (\Exception $e) {
            return $response->withJson(['success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()]], 500);
        }

        return $response->withJson(['success' => false], 400);
    }

    private function handleGoogleLogin($token)
    {
        $clientId = $this->container->get('settings.google.clientId');
        $client = new \Google_Client(['client_id' => $clientId]);
        $payload = $client->verifyIdToken($token);

        if(!$payload) { throw new InvalidTokenException; }

        $email = $payload['email'];
        $user = $this->userQuery->findOneByEmail($email);

        if($user === null) {
            $user = $this->userRegService->register($email, $password);
        }

        $picture = $payload['picture'];
        $accessToken = $this->getToken($user);

        $responseData = [
            "success" => true,
            "message" => "you are successfully logged in",
            "data" => [
                "id" => $user->getUuid(),
                "picture" => $picture,
                "email" => $user->getEmail(),
                "access_token" => (string) $accessToken,
            ]
        ];

        return $responseData;
    }

    private function handleFacebookLogin($token)
    {
        $fb = new \Facebook\Facebook([
            'app_id' => $this->container->get('settings.facebook.appId'),
            'app_secret' => $this->container->get('settings.facebook.appSecret'),
            'default_graph_version' => 'v2.10',
        ]);

        $oAuth2Client = $fb->getOAuth2Client();;
        $tokenMetaData = $oAuth2Client->debugToken($token);

        if(!$tokenMetaData->getIsValid()) { throw new InvalidTokenException; }

        $fbResponse = $fb->get('/me?fields=id,name,email,picture', $token);
        $profile = $fbResponse->getDecodedBody();

        $email = $profile['email'];
        $user = $this->userQuery->findOneByEmail($email);

        if($user === null) {
            $user = $this->userRegService->register($email, $password);
        }

        $picture = $profile['picture']['data']['url'];
        $accessToken = $this->getToken($user);

        $responseData = [
            "success" => true,
            "message" => "you are successfully logged in",
            "data" => [
                "id" => $user->getUuid(),
                "picture" => $picture,
                "email" => $user->getEmail(),
                "access_token" => (string) $accessToken,
            ]
        ];

        return $responseData;
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

    private function getToken($user)
    {
        $signer = new Sha256();
        $keychain = new Keychain();
        $token = (new Builder())->setIssuer('FREEDOM')
                        ->setIssuedAt(time())
                        ->setNotBefore(time() + 60)
                        ->setExpiration(time() + 3600)
                        ->set('uuid', $user->getUuid())
                        ->sign($signer, $keychain->getPrivateKey('file://' . __DIR__ . '/../../../key.pem'))
                        ->getToken();

        return $token;
    }
}
