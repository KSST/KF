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

namespace Koch\Tools;

/**
 * Description of SysInfoCache.
 */
class SysInfoCache
{
    /**
     * Returns a named array for usage in select/dropdown formelements.
     *
     * @return array Named Array.
     */
    public static function getNamedArray()
    {
        return [
            'xcache'       => self::hasXcache(),
            'wincache'     => self::hasWincache(),
            'apc'          => self::hasApc(),
            'eaccelerator' => self::hasEaccelerator(),
            'ioncube'      => self::hasIoncube(),
            'zend'         => self::hasZend(),
        ];
    }

    /**
     * Check for Xcache.
     *
     * @link http://xcache.lighttpd.net
     *
     * @return bool
     */
    public static function hasXcache()
    {
        return function_exists('xcache_isset');
    }

    /**
     * Check for Wincache.
     *
     * @link http://www.iis.net/expand/WinCacheForPHP
     *
     * @return bool
     */
    public static function hasWincache()
    {
        return function_exists('wincache_fcache_fileinfo');
    }

    /**
     * Check for Alternative PHP Cache.
     *
     * @link http://pecl.php.net/package/apc
     *
     * @return bool
     */
    public static function hasApc()
    {
        return function_exists('apc_add');
    }

    /**
     * Check for eAccelerator.
     *
     * @link http://eaccelerator.net
     *
     * @return bool
     */
    public static function hasEaccelerator()
    {
        return (bool) strlen(ini_get('eaccelerator.enable'));
    }

    /**
     * Check for ionCube Loader.
     *
     * @link http://www.php-accelerator.co.uk
     *
     * @return bool
     */
    public static function hasIoncube()
    {
        return (bool) strlen(ini_get('phpa'));
    }

    /**
     * Check for Zend Optimizer+.
     *
     * @link http://www.zend.com/products/server
     *
     * @return bool
     */
    public static function hasZend()
    {
        return (bool) strlen(ini_get('zend_optimizer.enable_loader'));
    }
}
