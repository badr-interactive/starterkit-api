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
    'settings.mail.host' => 'smtp.mailtrap.io',
    'settings.mail.port' => 2525,
    'settings.mail.enableAuth' => true,
    'settings.mail.username' => '23e9cb4290d03a',
    'settings.mail.password' => '6b517875957669',
    'settings.mail.sender' => 'no-reply@dev.badr.co.id',

    // DB Settings
    'settings.db.host' => '192.168.99.100',
    'settings.db.port' => '6666',
    'settings.db.user' => 'root',
    'settings.db.password' => 's3cr3t',
    'settings.db.database' => 'freedom',

    // Social login
    'settings.google.web.clientId' => '392223742967-d1ha3fpu60289nlinkvntcp93aubke8a.apps.googleusercontent.com',
    'settings.google.ios.clientId' => '392223742967-auqrva31v5dltj7k1o49l8bmpk2vav10.apps.googleusercontent.com',
    'settings.google.android.clientId' => '392223742967-vj98lnqp1as9d13b9tln6fouh903oc71.apps.googleusercontent.com',
    'settings.facebook.appId' => '106034073373462',
    'settings.facebook.appSecret' => '54e8843f0fa748ce1668e4615cff0796'
];
