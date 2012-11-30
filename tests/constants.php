<?php

/**
 * Koch Framework - Constants
 */

defined('CR')                       || define('CR', chr(13));                       // Carriage Return (\n)
defined('WWW_ROOT')                 || define('WWW_ROOT', 'http://');               // http://application
defined('WWW_ROOT_THEMES_CORE')     || define('WWW_ROOT_THEMES_CORE', 'http://');   // http://application/themes/core
defined('REWRITE_ENGINE_ON')        || define('REWRITE_ENGINE_ON', false);

defined('APPLICATION_NAME')         || define('APPLICATION_NAME', 'MYAPPLICATION');
defined('APPLICATION_PATH')         || define('APPLICATION_PATH', '/');
defined('APPLICATION_CACHE_PATH')   || define('APPLICATION_CACHE_PATH', sys_get_temp_dir() . '/');
defined('APPLICATION_MODULES_PATH') || define('APPLICATION_MODULES_PATH', '/Modules/');

defined('VENDOR_PATH')              || define('VENDOR_PATH', __DIR__ . '/../vendor/');

defined('APC')                      || define('APC', extension_loaded('apc'));
