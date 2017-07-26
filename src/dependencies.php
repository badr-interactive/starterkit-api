<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['auth'] = function ($c) {
    $authorization = str_replace('Bearer ', '', $c->request->getHeaderLine('Authorization'));
    $token = (new \Lcobucci\JWT\Parser())->parse((string) $authorization); // Parses from a string

    $user = \App\Modules\Auth\Model\UserQuery::create()->findOneByUuid($token->getClaim('uuid'));

    return $user;
};

// module specific deppendencies
$dir = __DIR__ . '/modules';
$files = scandir($dir);
foreach($files as $key => $value) {
    if($value == '.' || $value == '..') {
        continue;
    }

    $fullpath = $dir . '/' . $value;
    
    if(!is_dir($fullpath)) {
        continue;
    }

    if(!is_file($fullpath . '/dependencies.php')) {
        continue;
    }

    require $fullpath . '/dependencies.php';
}