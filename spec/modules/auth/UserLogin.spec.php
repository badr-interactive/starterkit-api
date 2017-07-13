<?php

describe('AuthController', function() {
     given('request', function() {
        $environment = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/json']);
        $request = \Slim\Http\Request::createFromEnvironment($environment);

        $data = [
            'email' => 'me@example.com',
            'password' => 'secret',
            'device_id' => '12aefebc-862c-4a7b-8f42-91f892dda5da',
            'fcm_token' => 'fRDb7AU9uKU:APA91bHr9Duptx6E5PTR7LPF9K3vgwLVitRvHIklHbjAiTDP4moZa3dyudcjhlp3Iv5e2s1HcNppNkUJaf68Q_hjvpHgWI'
        ];

        $body = $request->getBody();
        $body->write(json_encode($data));
        $request->withBody($body);

        return $request;
    });

    given('response', function() {
        return new \Slim\Http\Response();
    });

    describe('login() function', function() {
        it('should return HTTP Code 400 for invalid Content-Type', function() {
            $env = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/text']);
            $request = \Slim\Http\Request::createFromEnvironment($env);

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->login($request, $this->response);
            $resultBody = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(400);
            expect($resultBody->success)->toBe(false);
        });

        it('should return HTTP Code 401 if no email param', function() {
            $data = [
                'password' => 'secret',
                'device_id' => '12aefebc-862c-4a7b-8f42-91f892dda5da',
                'fcm_token' => 'fRDb7AU9uKU:APA91bHr9Duptx6E5PTR7LPF9K3vgwLVitRvHIklHbjAiTDP4moZa3dyudcjhlp3Iv5e2s1HcNppNkUJaf68Q_hjvpHgWI'
            ];

            $environment = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/json']);
            $request = \Slim\Http\Request::createFromEnvironment($environment);
            $body = $request->getBody();
            $body->write(json_encode($data));
            $request->withBody($body);

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->login($request, $this->response);
            $responseData = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(401);
            expect($responseData->success)->toBe(false);
        });

        it('should return HTTP Code 401 if no password param', function() {
            $data = [
                'email' => 'me@example.com',
                'device_id' => '12aefebc-862c-4a7b-8f42-91f892dda5da',
                'fcm_token' => 'fRDb7AU9uKU:APA91bHr9Duptx6E5PTR7LPF9K3vgwLVitRvHIklHbjAiTDP4moZa3dyudcjhlp3Iv5e2s1HcNppNkUJaf68Q_hjvpHgWI'
            ];

            $environment = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/json']);
            $request = \Slim\Http\Request::createFromEnvironment($environment);
            $body = $request->getBody();
            $body->write(json_encode($data));
            $request->withBody($body);

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->login($request, $this->response);
            $responseData = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(401);
            expect($responseData->success)->toBe(false);
        });

        it('should return HTTP Code 401 if no device_id param', function() {
            $data = [
                'email' => 'me@example.com',
                'password' => 'secret',
                'fcm_token' => 'fRDb7AU9uKU:APA91bHr9Duptx6E5PTR7LPF9K3vgwLVitRvHIklHbjAiTDP4moZa3dyudcjhlp3Iv5e2s1HcNppNkUJaf68Q_hjvpHgWI'
            ];

            $environment = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/json']);
            $request = \Slim\Http\Request::createFromEnvironment($environment);
            $body = $request->getBody();
            $body->write(json_encode($data));
            $request->withBody($body);

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->login($request, $this->response);
            $responseData = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(401);
            expect($responseData->success)->toBe(false);
        });

        it('should return HTTP Code 401 if no fcm_token param', function() {
            $data = [
                'email' => 'me@example.com',
                'password' => 'secret',
                'device_id' => '12aefebc-862c-4a7b-8f42-91f892dda5da'
            ];

            $environment = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/json']);
            $request = \Slim\Http\Request::createFromEnvironment($environment);
            $body = $request->getBody();
            $body->write(json_encode($data));
            $request->withBody($body);

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->login($request, $this->response);
            $responseData = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(401);
            expect($responseData->success)->toBe(false);
        });

        it('should return HTTP Code 200 if success', function() {
            $app = new \App\Modules\Auth\AuthController();
            $result = $app->login($this->request, $this->response);
            $responseData = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(200);
            expect($responseData->success)->toBe(true);
        });
    });
});