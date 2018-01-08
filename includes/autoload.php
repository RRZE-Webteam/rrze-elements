<?php

namespace RRZE\Elements;

defined('ABSPATH') || exit;

spl_autoload_register(function ($class) {
    $prefix = 'RRZE\Elements\\';
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $file = $base_dir . strtolower(str_replace('\\', '/', substr($class, $len))) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
