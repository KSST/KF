<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;
use Koch\Exception\Exception;

/**
 * Filter performing Startup Checks.
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
        // ensure smarty "tpl_compile" folder exists
        if(false === is_dir(ROOT_CACHE . 'tpl_compile') and
          (false === @mkdir(ROOT_CACHE .'tpl_compile', 0755, true))) {
            throw new Exception('Smarty Template Directories not existant.', 9);
        }

        // ensure smarty "cache" folder exists
        if(false === is_dir(ROOT_CACHE . 'tpl_cache') and
          (false === @mkdir(ROOT_CACHE .'tpl_cache', 0755, true))) {
            throw new Exception('Smarty Template Directories not existant.', 9);
        }

        // ensure smarty folders are writable
        if(false === is_writable(ROOT_CACHE . 'tpl_compile') or
          (false === is_writable(ROOT_CACHE . 'tpl_cache'))) {
            // if not, try to set writeable permission on the folders
            if((false === chmod(ROOT_CACHE . 'tpl_compile', 0755)) and
               (false === chmod(ROOT_CACHE . 'tpl_cache', 0755))) {
                throw new Exception('Smarty Template Directories not writable.', 10);
            }
        }
    }
}
