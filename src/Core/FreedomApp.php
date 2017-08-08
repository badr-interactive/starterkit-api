<?php

namespace App\Core;

use DI\ContainerBuilder;

class FreedomApp extends \DI\Bridge\Slim\App
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions(__DIR__ . '/../config.php');
    }
}