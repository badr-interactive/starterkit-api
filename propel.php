<?php

require_once 'vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

return [
    'propel' => [
        'database' => [
            'connections' => [
                'localhost' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\DebugPDO',
                    'dsn'        => 'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'),
                    'user'       => getenv('DB_USERNAME'),
                    'password'   => getenv('DB_PASSWORD'),
                    'attributes' => []
                ],
            ]
        ],
        'paths' => [
            'schemaDir' => __DIR__.'/src/database',
            'outputDir' => '%schemaDir%',
            'phpDir' => '%outputDir%/model',
            'phpConfDir' => '%outputDir%/config',
            'sqlDir' => '%outputDir%/sql',
            'migrationDir' => '%outputDir%/migrations',

        ],
        'runtime' => [
            'defaultConnection' => 'localhost',
            'connections' => ['localhost']
        ],
        'generator' => [
            'defaultConnection' => 'localhost',
            'connections' => ['localhost']
        ]
    ]
];