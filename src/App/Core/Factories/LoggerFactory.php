<?php

namespace App\Core\Factories;

use DI\Container;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\JSONFormatter;

class LoggerFactory
{
    const MAX_LOG_FILE = 30;

    public static function create()
    {
        $logfile = __DIR__ . '/../../../../logs/app.json';

        $formatter = new JSONFormatter();
        $handler = new RotatingFileHandler($logfile, self::MAX_LOG_FILE);
        $handler->setFormatter($formatter);

        $logger = new Logger('Freedom');
        $logger->pushHandler($handler);

        return $logger;
    }
}
