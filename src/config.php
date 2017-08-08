<?php

return [

    // Framework Settings
    'settings.httpVersion' => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails' => false,
    'settings.addContentLengthHeader' => true,
    'settings.routerCacheFile' => false,

    // Mail settings
    'settings.mail.host' => 'mailhog',
    'settings.mail.port' => 1025,
    'settings.mail.enableAuth' => true,
    'settings.mail.username' => '',
    'settings.mail.password' => '',
    'settings.mail.sender' => 'no-reply@example.com',

    // DB Settings
    'settings.db.host' => 'mysql',
    'settings.db.port' => '3306',
    'settings.db.user' => 'root',
    'settings.db.password' => 's3cr3t',
    'settings.db.database' => 'freedom'
];
