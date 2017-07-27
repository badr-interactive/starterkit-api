<?php
$container['User'] = function($c) {
    return new App\Modules\Auth\Model\User;
};

$container['UserQuery'] = function($c) {
    return new App\Modules\Auth\Model\UserQuery;
};
