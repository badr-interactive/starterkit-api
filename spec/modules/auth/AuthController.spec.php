<?php

describe('Auth Controller', function() {
    given('request', function() {
        $environment = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/json']);
        $request = \Slim\Http\Request::createFromEnvironment($environment);

        return $request;
    });

    given('response', function() {
        return new \Slim\Http\Response();
    });

    describe('register() function', function() {
        it('should return HTTP Code 400 for invalid Content-Type', function() {
            $env = \Slim\Http\Environment::mock(['CONTENT_TYPE' => 'application/text']);
            $request = \Slim\Http\Request::createFromEnvironment($env);

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($request, $this->response);
            $resultBody = json_decode((string) $result->getBody());
            expect($result->getStatusCode())->toBe(400);
            expect($resultBody->success)->toBe(false);
        });
        
        it('should returns valid json string', function() {
            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($this->request, $this->response);
            json_decode((string)$result->getBody());

            expect($result->getHeader('Content-Type')[0])->toBe('application/json;charset=utf-8');
            expect(json_last_error() == JSON_ERROR_NONE)->toBe(true);
        });

        it('should returns HTTP Code 200 when success', function() {
            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($this->request, $this->response);

            expect($result->getStatusCode())->toBe(200);
        });

        it('should return success true', function() {
            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($this->request, $this->response);
            

            $resultJson = json_decode((string) $result->getBody());
            expect($resultJson->success)->toBe(true);
        });
    });
});