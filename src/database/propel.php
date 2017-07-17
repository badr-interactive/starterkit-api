<?php

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