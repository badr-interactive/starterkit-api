<?php

require __DIR__ . '/../../base.php';

describe('Auth Controller', function() {
    it('should only receive POST method', function() {
        $response = runApp('GET', '/auth/register');
        expect($response->getStatusCode())->toBe(405);
    });
});