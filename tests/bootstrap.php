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

/**
 * Configuration Options for Tests
 * ===================================================
 */

// Toggle for CodeCoverage. (It depends on the PHP extensions Xdebug and SQlite.)
define('PERFORM_CODECOVERAGE', false);

// Toggle, whether to run WebTests or not.
define('PERFORM_WEBTESTS', false);

// ===================================================

// Error Reporting Level
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('html_errors', false);
ini_set('log_errors', false);

// Tests take some time and memory
/**
 * The test for safe_mode is needed in order to avoid the message:
 * "Warning: set_time_limit() has been disabled for security reasons".
 * SAFE_MODE has been DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0.
 * This check is added to get tests without errors on crappy, outdated hosters.
 */
if (ini_get('safe_mode') == false) { set_time_limit(0); }
ini_set('memory_limit', '256M');

// PHP Version Check
$REQUIRED_PHP_VERSION = '5.3.2';
if (version_compare(PHP_VERSION, $REQUIRED_PHP_VERSION, '<=') === true) {
    exit('Your PHP Version is <b><font color="#FF0000">' . PHP_VERSION . '</font></b>.
         Clansuite Testsuite requires PHP Version <b><font color="#4CC417">' . $REQUIRED_PHP_VERSION . '</font></b> or newer.');
}
unset($REQUIRED_PHP_VERSION);

// well this should be defined in PHP.ini.. fallback, if you are lazy.
date_default_timezone_set('Europe/Berlin');

$paths = array(
    // add the TEST SUBJECT dir
    realpath(dirname(__DIR__) . '/framework'),
    realpath(dirname(__DIR__)),
    // adjust include path to TESTS dir
    realpath(__DIR__),
    realpath(__DIR__ . '/KochTests'),
);
#var_dump($paths);

// attach original include paths
set_include_path(implode($paths, PATH_SEPARATOR) . PATH_SEPARATOR . get_include_path());
unset($paths);

// needed if, run from CLI
if (empty($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = gethostname();
}

// Constants
define('REWRITE_ENGINE_ON', 1);
define('TESTSUBJECT_DIR', dirname(__DIR__) . '/');
define('KOCH_FRAMEWORK', dirname(ROOT) . '/framework/Koch/');

// Autoloader
include KOCH_FRAMEWORK . 'Autoload/Loader.php';
new \Koch\Autoload\Loader();

\Koch\Localization\Utf8::initialize();

/**
 * We might need some debug utils,
 * when we are not in CLI mode.
 */
if (isCli() === false) {
    require_once KOCH_FRAMEWORK . 'Debug/Debug.php';
}

/**
 * Gettext Replacement
 *
 * @param type $msgid
 */
if (!function_exists('_')) {
    function _($msgid)
    {
        return $msgid;
    }
}

function isCli()
{
    if (php_sapi_name() == 'cli' and empty($_SERVER['REMOTE_ADDR'])) {
        return true;
    } else {
        return false;
    }
}

// put more bootstrapping code here

/**
 * Netbeans Hint
 *
 * Project > Properties > PHPUnit
 * In the Project Properties Dialog
 * 1) Activate Checkbox "Use Bootstrap"
 * 2) Activate Checkbox "Use Bootstrap for Creating New Unit Tests"
 * 3) Use "Browse" and point to this file
 */
