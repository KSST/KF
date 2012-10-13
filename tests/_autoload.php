<?php
/**
 * Setup autoloading
 */

// Koch Framework Autoloader
include_once __DIR__ . '/../framework/Koch/Autoload/Loader.php';
$autoloader = new \Koch\Autoload\Loader();
$autoloader->setClassMapFile(__DIR__ . '/autoloader.classmap.php'); // KF/tests/autoloader.classmap.php

// Composer Autoloader
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
}
