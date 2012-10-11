<?php

/**
 * Clansuite - just an eSports CMS
 * Jens A. Koch © 2005 - onwards
 * http://www.clansuite.com/
 *
 * This file is part of "Clansuite - just an eSports CMS".
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * *
 */

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

if (!function_exists('_')) {
    function _($msgid)
    {
        return $msgid;
    }
}

// attach original include paths
set_include_path(implode(PATH_SEPARATOR, $paths));

include __DIR__ . '/_autoload.php';

\Koch\Localization\Utf8::initialize();
