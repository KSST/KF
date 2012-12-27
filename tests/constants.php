<?php

/**
 * Koch Framework - Constants
 */

defined('CR')                       || define('CR', chr(13));                       // Carriage Return (\n)

defined('WWW_ROOT')                 || define('WWW_ROOT', 'http://');               // http://application
defined('WWW_ROOT_THEMES')          || define('WWW_ROOT_THEMES', 'http://');        // http://application/themes
defined('WWW_ROOT_THEMES_CORE')     || define('WWW_ROOT_THEMES_CORE', 'http://');   // http://application/themes/core
defined('WWW_ROOT_THEMES_FRONTEND') || define('WWW_ROOT_THEMES_FRONTEND', 'http://'); // http://application/themes/frontend
defined('WWW_ROOT_THEMES_BACKEND')  || define('WWW_ROOT_THEMES_BACKEND', 'http://');  // http://application/themes/backend

defined('APPLICATION_NAME')         || define('APPLICATION_NAME', 'TESTAPPLICATION');
defined('APPLICATION_VERSION')      || define('APPLICATION_VERSION', '1.0.0');
defined('APPLICATION_VERSION_STATE')|| define('APPLICATION_VERSION_STATE', 'alpha-omega');
defined('APPLICATION_VERSION_NAME') || define('APPLICATION_VERSION_NAME', 'Aguila');

defined('APPLICATION_PATH')         || define('APPLICATION_PATH', __DIR__ . '/fixtures/APP/');
defined('APPLICATION_CACHE_PATH')   || define('APPLICATION_CACHE_PATH', sys_get_temp_dir() . '/');
defined('APPLICATION_MODULES_PATH') || define('APPLICATION_MODULES_PATH', '/Modules/');

defined('VENDOR_PATH')              || define('VENDOR_PATH', __DIR__ . '/../vendor/');

defined('APC')                      || define('APC', extension_loaded('apc'));

defined('REWRITE_ENGINE_ON')        || define('REWRITE_ENGINE_ON', false);
defined('DEBUG')                    || define('DEBUG', true);
