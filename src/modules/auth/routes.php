<?php

$app->post('/auth/register', \App\Modules\Auth\AuthController::class . ':register');