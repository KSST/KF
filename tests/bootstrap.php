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

include __DIR__ . '/_autoload.php';

\Koch\Localization\Utf8::initialize();

include __DIR__ . '/constants.php';
