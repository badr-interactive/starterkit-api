<?php

require __DIR__ . '/../../base.php';

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
        $data = json_encode([
            'email' => 'me@example.com',
            'password' => 'secret',
            'confirmation_password' => 'secret']);
        $response = runApp('POST', '/auth/register', $data);
        $parsedBody = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(200);
        expect($parsedBody->success)->toBe(true);
    });
});