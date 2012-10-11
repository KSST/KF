<?php
// Error Reporting Level
error_reporting(E_ALL | E_STRICT);

$root = realpath(dirname((__DIR__)));
$core = "$root/framework";
$test = "$root/tests";

$paths = array(
    $core,
    $test,
    get_include_path()
);
// attach original include paths
set_include_path(implode(PATH_SEPARATOR, $paths));

if (!function_exists('_')) {
    function _($msgid)
    {
        return $msgid;
    }
}

include __DIR__ . '/_autoload.php';

\Koch\Localization\Utf8::initialize();
