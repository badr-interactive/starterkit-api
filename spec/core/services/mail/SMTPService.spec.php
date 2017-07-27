<?php

use App\Core\Services\Mail\SMTPService;

describe('SMTP Mail Service', function() {
    given('container', function() {
        $settings = [
            'host' => 'localhost',
            'port' => 25,
            'username' => '',
            'password' => '',
            'from' => 'no-reply@example.com'
        ];
        
        $container = Phake::mock(Slim\Container::class);
        Phake::when($container)->get('settings')->thenReturn([
            'mail' => $settings
        ]);

        return $container;
    });

    it('should load default settings',function() {
        $settings = $this->container->get('settings')['mail'];
        $service = new SMTPService($this->container);
        expect($service->Host)->toBe($settings['host']);
        expect($service->Port)->toBe($settings['port']);
        expect($service->Username)->toBe($settings['username']);
        expect($service->Password)->toBe($settings['password']);
        expect($service->From)->toBe($settings['from']);
    });

    it('should able to alter config', function() {
        $service = new SMTPService($this->container);
        expect($service->Host)->toBe('localhost');
        $service->Host = 'example.com';
        expect($service->Host)->toBe('example.com');

        expect($service->Port)->toBe(25);
        $service->Port = 1225;
        expect($service->Port)->toBe(1225);
    });
});