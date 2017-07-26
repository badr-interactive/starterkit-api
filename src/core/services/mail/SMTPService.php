<?php

namespace App\Core\Services\Mail;

use Slim\Container;

class SMTPService
{
    protected $settings = [];

    function __construct(Container $container)
    {
        $this->settings = $container->get('settings')['mail'];
    }

    public function set($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function getSettings()
    {
        return $this->settings;
    }
}