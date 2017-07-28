<?php

require __DIR__ . '/../../Base.php';

describe('Forgot Password', function() {
    it('should reject if doesnt have email param', function() {
        $response = runApp('POST', '/auth/forgot_password');
        $responseData = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(400);
        expect($responseData->success)->toBe(false);
    });

    it('should generate reset token', function() {
        $mockUser = Phake::mock(App\Modules\Auth\Model\User::class);

        $dependencies = [];
        $dependencies['User'] = function($c) use($mockUser) {
            return $mockUser;
        };

        $data = ['email' => 'me@example.com'];
        $response = runApp('POST', '/auth/forgot_password', json_encode($data), $dependencies);
        $responseData = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(200);
        expect($responseData->success)->toBe(true);
    });
});