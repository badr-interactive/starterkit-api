<?php

require __DIR__ . '/../../Base.php';

describe('Forgot Password', function() {
    it('should reject if doesnt have email param', function() {
        $response = runApp('POST', '/auth/forgot_password');
        $responseData = json_decode((string)$response->getBody());
        expect($response->getStatusCode())->toBe(400);
        expect($responseData->success)->toBe(false);
    });

    it('should silent fail when email not found', function() {
        $mockUserQuery = Phake::mock(App\Modules\Auth\Model\UserQuery::class);
        Phake::when($mockUserQuery)->thenReturn($mockUserQuery);
        Phake::when($mockUserQuery)->findOneByEmail(Phake::ignoreRemaining())->thenReturn(null);
        
        $dependencies = [];
        $dependencies['UserQuery'] = function($c) use ($mockUserQuery) {
            return $mockUserQuery;
        };

        $data = ['email' => 'me@example.com'];
        $response = runApp('POST', '/auth/forgot_password', json_encode($data), $dependencies);
        expect($response->getStatusCode())->toBe(200);
    });
});