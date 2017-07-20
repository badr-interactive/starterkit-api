<?php

$container['AuthController'] = function($c) {
    $user = new App\Modules\Auth\Model\User;
    return new App\Modules\Auth\AuthController($user);
};