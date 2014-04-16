<?php
// Error Reporting Level
error_reporting(E_ALL | E_STRICT);

// add framework and tests to include path
$framework  = realpath(__DIR__ . '/../framework');
$tests      = realpath(__DIR__ . '/../tests');

$paths = array(
    $framework,
    $tests,
    get_include_path() // attach original include paths
);
set_include_path(implode(PATH_SEPARATOR, $paths));

// gettext fall through
if (!function_exists('_')) {
    function _($msgid)
    {
        return $msgid;
    }
}

// Composer Autoloader
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
} else {
    echo '[Error] Koch Framework > Tests > Bootstrap: Could not find "vendor/autoload.php".' . PHP_EOL;
    echo 'Did you forget to run "composer install --dev"?' . PHP_EOL;
    exit(1);
}

// Koch Framework Autoloader
// Note: We are on top of the autoloader stack and not Composer's ClassLoader.
require_once __DIR__ . '/../framework/Koch/Autoload/Loader.php';
$autoloader = new \Koch\Autoload\Loader();
$autoloader->setClassMapFile(__DIR__ . '/autoloader.classmap.php'); // KF/tests/autoloader.classmap.php

\Koch\Localization\Utf8::initialize();

require_once __DIR__ . '/constants.php';
