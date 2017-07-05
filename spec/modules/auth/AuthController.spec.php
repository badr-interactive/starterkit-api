<?php

describe('Auth Controller', function() {
    describe('register()', function() {
        it('returns valid json string', function() {
            $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/auth/request',
                'CONTENT_TYPE' => 'application/json',
            ]);

            $response = new \Slim\Http\Response();
            $request = \Slim\Http\Request::createFromEnvironment($environment);
            $requestBody = $request->getBody();
            $requestBody->write(json_encode(['name' => 'salman']));

            $app = new \App\Modules\Auth\AuthController();
            $result = $app->register($request, $response);
            json_decode((string)$result->getBody());

            expect($result->getHeader('Content-Type')[0])->toBe('application/json;charset=utf-8');
            expect(json_last_error() == JSON_ERROR_NONE)->toBe(true);
        });
    });
});