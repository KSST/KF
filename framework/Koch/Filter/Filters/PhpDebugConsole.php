<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Koch Framework - Filter for displaying the Debugging Console.
 */
class PhpDebugConsole implements FilterInterface
{
    private $config = null;

    public function __construct(Koch\Config $config)
    {
        $this->config = $config;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        // webdebug must be enabled in configuration
        if (isset($this->config['error']['webdebug']) and $this->config['error']['webdebug'] === 1) {
            return;
        }

        // DEBUG mode must be on
        if (defined('DEBUG') and DEBUG === true) {
            return;
        }

        /*
         * ================================================
         *  Initialize PHP_Debug Web-Debugging Console
         * ================================================
         */

        // Additional ini path for PHPDEBUG
        define('ADD_PHPDEBUG_ROOT', ROOT_LIBRARIES . 'phpdebug');
        set_include_path(ADD_PHPDEBUG_APPLICATION_PATH . PATH_SEPARATOR . get_include_path());

        // Load Library
        if (false === class_exists('PHP_Debug', false)) {
            include ROOT_LIBRARIES . 'phpdebug/PHP/Debug.php';
        }

        // Setup Options for the PHPDebug Object
        $options = [
            // General Options
            'render_type'          => 'HTML',    // Renderer type
            'render_mode'          => 'div',     // Renderer mode
            'restrict_access'      => false,     // Restrict access of debug
            'allow_url_access'     => true,      // Allow url access
            'url_key'              => 'key',     // Url key
            'url_pass'             => 'nounou',  // Url pass
            'enable_watch'         => true,      // Enable wath of vars
            'replace_errorhandler' => true,      // Replace the php error handler
            'lang'                 => 'EN',      // Lang

            // Renderer specific
            'HTML_DIV_view_source_script_name'  => APPLICATION_PATH . 'libraries/phpdebug/PHP_Debug_ShowSource.php',
            'HTML_DIV_images_path'              => WWW_ROOT . 'libraries/phpdebug/images',
            'HTML_DIV_css_path'                 => WWW_ROOT . 'libraries/phpdebug/css',
            'HTML_DIV_js_path'                  => WWW_ROOT . 'libraries/phpdebug/js',
            'HTML_DIV_remove_templates_pattern' => true,
            #'HTML_DIV_templates_pattern' => array('/var/www-protected/php-debug.com' => '/var/www/php-debug')
        ];

        // Initialiaze Object
        $debug = new PHP_Debug($options);

        // Set Title to Debug Console
        $debug->add('Koch Framework DEBUG INFO');

        /*
         *  Load JS / CSS for PHP Debug Console into the Output Buffer
         */
        $html = '<script type="text/javascript" src="' . $options['HTML_DIV_js_path'] . '/html_div.js"></script>';
        $html .= '<link rel="stylesheet" type="text/css"';
        $html .= ' media="screen" href="' . $options['HTML_DIV_css_path'] . '/html_div.css" />';

        unset($options);

        // combine the html output
        $debugbarHTML = $html . $debug->getOutput();

        // push output into event object
        $event = new DebugConsoleResponse_Event($debugbarHTML);

        // and output the debugging console at the end of the application runtime
        \Koch\Event\Dispatcher::instantiate()->addEventHandler('onApplicationShutdown', $event);
    }
}
