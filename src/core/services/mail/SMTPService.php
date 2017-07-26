<?php

namespace App\Core\Services\Mail;

use Slim\Container;

class SMTPService extends \PHPMailer
{
    protected $recipients = [];
    protected $subject = "";
    protected $body = "";

    function __construct(Container $container)
    {
        parent::__construct();
        $settings = $container->get('settings')['mail'];

        $this->isSMTP();
        $this->Host = $settings['host'];
        $this->Port = $settings['port'];
        $this->SMTPAuth = true;
        $this->Username = $settings['username'];
        $this->Password = $settings['password'];
        $this->From = $settings['from'];
    }
}