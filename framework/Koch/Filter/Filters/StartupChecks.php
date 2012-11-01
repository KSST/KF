<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
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
 *
 */

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\Exception\Exception;

/**
 * Koch Framework - Filter performing Startup Checks.
 *
 * Purpose: Perform Various Startup Check before running a Koch Framework Module.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Filters
 */
class StartupChecks implements FilterInterface
{
    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        /**
         * Deny service, if the system load is too high.
         */
        if (defined('DEBUG') and DEBUG == false) {
            $maxServerLoad = isset(self::$config['load']['max']) ? (float) self::$config['load']['max'] : 80;

            if (\Koch\Functions\Functions::getServerLoad() > $maxServerLoad) {
                $retry = (int) mt_rand(45, 90);
                header('Retry-After: ' . $retry);
                header('HTTP/1.1 503 Too busy, try again later');
                die('HTTP/1.1 503 Server too busy. Please try again later.');
            }
        }

        // ensure smarty "tpl_compile" folder exists
        if (false === is_dir(APPLICATION_CACHE_PATH . 'tpl_compile') and
            (false === @mkdir(APPLICATION_CACHE_PATH . 'tpl_compile', 0755, true))) {
            throw new Exception('Smarty Template Directories not existant.', 9);
        }

        // ensure smarty "cache" folder exists
        if (false === is_dir(APPLICATION_CACHE_PATH . 'tpl_cache') and
            (false === @mkdir(APPLICATION_CACHE_PATH . 'tpl_cache', 0755, true))) {
            throw new Exception('Smarty Template Directories not existant.', 9);
        }

        // ensure smarty folders are writable
        if (false === is_writable(APPLICATION_CACHE_PATH . 'tpl_compile') or
            (false === is_writable(APPLICATION_CACHE_PATH . 'tpl_cache'))) {
            // if not, try to set writeable permission on the folders
            if ((false === chmod(APPLICATION_CACHE_PATH . 'tpl_compile', 0755)) and
                (false === chmod(APPLICATION_CACHE_PATH . 'tpl_cache', 0755))) {
                throw new Exception('Smarty Template Directories not writable.', 10);
            }
        }
    }
}
