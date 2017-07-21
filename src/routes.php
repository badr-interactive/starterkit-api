<?php

$dir = __DIR__ . '/modules';
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

    require $fullpath . '/routes.php';
}
