<?php

$app->post('/auth/register', '\App\Modules\Auth\AuthController:register');

$app->post('/auth/login', '\App\Modules\Auth\LoginController:login');
