<?php

describe('SMTP Mail Service', function() {
    given('container', function() {
        $settings = [
            'host' => 'localhost',
            'port' => 25,
            'username' => '',
            'password' => ''
        ];
        
        $container = Phake::mock(Slim\Container::class);
        Phake::when($container)->get('settings')->thenReturn([
            'mail' => $settings
        ]);

        return $container;
    });

    it('should load settings from slim settings.php', function() {
        $service = new App\Core\Services\Mail\SMTPService($this->container);
        $serviceSettings = $service->getSettings();
        expect($serviceSettings['host'])->toBe('localhost');
        expect($serviceSettings['port'])->toBe(25);
        expect($serviceSettings['username'])->toBe('');
        expect($serviceSettings['password'])->toBe('');
    });

    it('should be able to override default settings', function() {
        $service = new App\Core\Services\Mail\SMTPService($this->container);
        $service->set('host', 'example.com');
        expect($service->getSettings()['host'])->toBe('example.com');
        expect($service->getSettings()['port'])->toBe(25);

        $service->set('port', 1025);
        expect($service->getSettings()['port'])->toBe(1025);
        expect($service->getSettings()['username'])->toBe('');
    });

    it('should be able to send message', function() {
        $service = new App\Core\Services\Mail\SMTPService($this->container);
        $from = "me@example.com";
        $to = "you@example.com";
        $subject = "Let's meet at Office!";
        $result = $service->send($from, $to, $subject);
    });
});