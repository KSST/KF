<?php
/**
 * Setup autoloading
 */

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
} else {
    include_once __DIR__ . '/../framework/Koch/Autoload/Loader.php';
    $autoloader = new \Koch\Autoload\Loader();
    $autoloader->setClassMapFile(__DIR__ . '/autoloader.classmap.php'); // KF/tests/autoloader.classmap.php
}