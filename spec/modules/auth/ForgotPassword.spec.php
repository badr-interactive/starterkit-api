<?php

require_once __DIR__ . '/../../Base.php';

describe('Forgot Password', function() {
    it('should reject if doesnt have email param', function() {
        $response = runApp('POST', '/auth/forgot_password');
        $responseData = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(400);
        expect($responseData->success)->toBe(false);
    });
});