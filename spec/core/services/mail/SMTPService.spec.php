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

    it('should able to send message to server', function() {
        $service = new SMTPService($this->container);
        $service->Port = 1025;
        $service->Subject = 'Mail from Heaven';
        $service->Body = 'Hello Dude!';

        $to = uniqid("me_") . '@example.com';
        $service->addAddress($to, 'Me Boo');
        $result = $service->send();
        expect($result)->toBe(true);

        $url = "http://localhost:8025/api/v2/search?kind=to&query=" . $to;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        expect($response->total)->toBe(1);
    });
});