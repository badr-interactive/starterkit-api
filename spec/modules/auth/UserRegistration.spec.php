<?php

require __DIR__ . '/../../Base.php';

describe('Auth Controller', function() {
    it('should reject if does not have email param', function() {
        $data = json_encode([
            'password' => 'secret',
            'confirmation_password' => 'secret']);
        $response = runApp('POST', '/auth/register', $data);
        $parsedBody = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(400);
        expect($parsedBody->success)->toBe(false);
    });

    it('should reject if does not have password param', function() {
        $data = json_encode([
            'email' => 'me@example.com',
            'confirmation_password' => 'secret']);
        $response = runApp('POST', '/auth/register', $data);
        $parsedBody = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(400);
        expect($parsedBody->success)->toBe(false);
    });

    it('should reject if does not have confirmation_password param', function() {
        $data = json_encode([
            'email' => 'me@example.com',
            'password' => 'secret']);
        $response = runApp('POST', '/auth/register', $data);
        $parsedBody = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(400);
        expect($parsedBody->success)->toBe(false);
    });

    it('should save data into DB', function() {
        $dependencies = [];
        $mockUser = Phake::mock(App\Modules\Auth\Model\User::class);
        $dependencies['User'] = function($c) use($mockUser) {
            return $mockUser;
        };

        $data = [
            'email' => 'me@example.com',
            'password' => 'secret',
            'confirmation_password' => 'secret' ];

        $response = runApp('POST', '/auth/register', json_encode($data), $dependencies);
        expect($response->getStatusCode())->toBe(200);

        $parsedBody = json_decode((string)$response->getBody());
        expect($parsedBody->success)->toBe(true);

        Phake::verify($mockUser)->setEmail(Phake::ignoreRemaining());
        Phake::verify($mockUser)->setPassword(Phake::ignoreRemaining());
        Phake::verify($mockUser)->setUuid(Phake::ignoreRemaining());
        Phake::verify($mockUser)->setCreatedAt(Phake::ignoreRemaining());
        Phake::verify($mockUser)->save();
    });
});