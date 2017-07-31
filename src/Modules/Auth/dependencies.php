<?php

$container['User'] = function($c) {
    return new App\Modules\Auth\Model\User;
};

$container['UserQuery'] = function($c) {
    return App\Modules\Auth\Model\UserQuery::create();
};

$container['ResetToken'] = function($c) {
    return new App\Modules\Auth\Model\ResetToken;
};

$container['ResetTokenQuery'] = function($c) {
    return App\Modules\Auth\Model\ResetTokenQuery::create();
};

