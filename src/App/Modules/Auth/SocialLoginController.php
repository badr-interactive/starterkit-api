<?php

namespace App\Modules\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Modules\Auth\Model\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use DI\Container;
use Ramsey\Uuid\Uuid;

class SocialLoginController
{
    function __construct(Container $container, User $user)
    {
        $this->container = $container;
        $this->user = $user;
    }

    public function login(Request $request, Response $response)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if($contentType !== 'application/json') {
            return $response->withJson(['success' => false], 400);
        }

        $params = $request->getParsedBody();
        $checklist = ['provider', 'token'];
        if(!$this->validateRequiredParam($checklist, $request)) {
            return $response->withJson(['success' => false], 400);
        }

        $provider = $params['provider'];
        $idToken = $params['token'];

        if($provider === 'google') {
            $clientId = $this->container->get('settings.google.clientId');
            $client = new \Google_Client(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($idToken);

            if($payload) {
                $userId = $payload['sub'];
                $email = $payload['email'];

                $uuid = Uuid::uuid4()->toString();
                $password = substr(uniqid(), -6);
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $this->user->setEmail($email);
                $this->user->setPassword($hashedPassword);
                $this->user->setUuid($uuid);

                $createdAt = new \DateTime();
                $this->user->setCreatedAt($createdAt->format('Y-m-d H:i:s'));
                $this->user->save();

                $token = $this->getToken($request, $this->user);
                $responseData = [
                    "success" => true,
                    "message" => "you are successfully logged in",
                    "data" => [
                        "id" => $this->user->getUuid(),
                        "name" => $this->user->getEmail(),
                        "photo" => $request->getUri()->getBaseUrl() . '/users/' . $this->user->getUuid() . '/avatar/',
                        "email" => $this->user->getEmail(),
                        "access_token" => (string) $token,
                    ]
                ];

                return $response->withJson($responseData, 200);
            } else {
                return $response->withJson(['success' => false], 401);
            }
        } else if($provider === 'facebook') {
            $accessToken = $params['token'];
            $fb = new \Facebook\Facebook([
                'app_id' => '102910203769316', // Replace {app-id} with your app id
                'app_secret' => 'ec381697de304451fee86d52dfd9cdc0',
                'default_graph_version' => 'v2.10',
            ]);

            try {
                $oAuth2Client = $fb->getOAuth2Client();
                $tokenMetaData = $oAuth2Client->debugToken($accessToken);
                $fbResponse = $fb->get('/me?fields=id,name,email', $accessToken);
                $profile = $fbResponse->getDecodedBody();

                $userId = $profile['id'];
                $email = $profile['email'];
                $uuid = Uuid::uuid4()->toString();
                $password = substr(uniqid(), -6);
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $this->user->setEmail($email);
                $this->user->setPassword($hashedPassword);
                $this->user->setUuid($uuid);
                $createdAt = new \DateTime();
                $this->user->setCreatedAt($createdAt->format('Y-m-d H:i:s'));
                $this->user->save();

                $token = $this->getToken($request, $this->user);
                $responseData = [
                    "success" => true,
                    "message" => "you are successfully logged in",
                    "data" => [
                        "id" => $this->user->getUuid(),
                        "name" => $this->user->getEmail(),
                        "photo" => $request->getUri()->getBaseUrl() . '/users/' . $this->user->getUuid() . '/avatar/',
                        "email" => $this->user->getEmail(),
                        "access_token" => (string) $token,
                    ]
                ];

                return $response->withJson($responseData, 200);
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                return $response->withJson(['success' => false], 401);
            }
        }
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

    private function getToken($request, $user)
    {
        $signer = new Sha256();
        $keychain = new Keychain();
        $token = (new Builder())->setIssuer($request->getUri()) // Configures the issuer (iss claim)
                        ->setAudience($request->getUri()) // Configures the audience (aud claim)
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
                        ->set('uuid', $user->getUuid()) // Configures a new claim, called "uid"
                        ->sign($signer, $keychain->getPrivateKey('file://' . __DIR__ . '/../../../key.pem'))
                        ->getToken(); // Retrieves the generated token

        return $token;
    }
}
