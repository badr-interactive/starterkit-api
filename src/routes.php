<?php
$app = new \Slim\App();
$dir = '/usr/share/nginx/src/modules';
$files = scandir($dir);
foreach($files as $key => $value) {
    if($value == '.' || $value == '..') {
        continue;
    }

    $fullpath = $dir . '/' . $value;
    
    if(!is_dir($fullpath)) {
        continue;
    }

    if(!is_file($fullpath . '/routes.php')) {
        continue;
    }

    include_once $fullpath . '/routes.php';
}
