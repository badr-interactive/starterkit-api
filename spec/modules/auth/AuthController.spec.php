<?php

describe('Auth Controller', function() {
    given('request', function() {
        $environment = \Slim\Http\Environment::mock([]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $requestBody = $request->getBody();
        $requestBody->write(json_encode(['name' => 'salman']));
        $request->withBody($requestBody);

        return $request;
    });

    given('response', function() {
        return new \Slim\Http\Response();
    });
    
    describe('register()', function() {
        it('should returns valid json string', function() {
            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($this->request, $this->response);
            json_decode((string)$result->getBody());

            expect($result->getHeader('Content-Type')[0])->toBe('application/json;charset=utf-8');
            expect(json_last_error() == JSON_ERROR_NONE)->toBe(true);
        });

        it('should returns HTTP Code 200', function() {
            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($this->request, $this->response);
            json_decode((string)$result->getBody());

            expect($result->getStatusCode())->toBe(200);
        });
    });
});