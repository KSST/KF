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

namespace Koch\Tools;

/**
 * Koch Framework - Systeminfo
 *
 * The class provides pieces of information about the environment.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  SystemInfo
 */
class SysInfo
{
    /**
     * @var $extensions
     */
    private static $extensions;

    public static function getLoadedExtensions()
    {
        if (null === self::$extensions) {
            self::$extensions = get_loaded_extensions();
        }
    }

    public static function isLoadedExtension($extension_name)
    {
        self::getLoadedExtensions();

        return in_array($extension_name, self::$extensions);
    }
}

class SystemInfo_Cache
{
    /**
     * Returns a named array for usage in select/dropdown formelements.
     *
     * @return array Named Array.
     */
    public static function getNamedArray()
    {
        return array(
            'xcache'        => self::hasXcache(),
            'wincache'      => self::hasWincache(),
            'apc'           => self::hasApc(),
            'eaccelerator'  => self::hasEaccelerator(),
            'ioncube'       => self::hasIoncube(),
            'zend'          => self::hasZend(),
            'nusphere'      => self::hasNusphere()
        );
    }

    /**
     * Check for Xcache
     *
     * @link http://xcache.lighttpd.net
     * @return bool
     */
    public static function hasXcache()
    {
        return function_exists('xcache_isset');
    }

    /**
     * Check for Wincache
     *
     * @link http://www.iis.net/expand/WinCacheForPHP
     * @return bool
     */
    public static function hasWincache()
    {
        return function_exists('wincache_fcache_fileinfo');
    }

    /**
     * Check for Alternative PHP Cache
     *
     * @link http://pecl.php.net/package/apc
     * @return bool
     */
    public static function hasApc()
    {
        return function_exists('apc_add');
    }

    /**
     * Check for eAccelerator
     *
     * @link http://eaccelerator.net
     * @return bool
     */
    public static function hasEaccelerator()
    {
        return (bool) strlen(ini_get('eaccelerator.enable'));
    }

    /**
     * Check for ionCube Loader
     *
     * @link http://www.php-accelerator.co.uk
     * @return bool
     */
    public static function hasIoncube()
    {
        return (bool) strlen(ini_get('phpa'));
    }

    /**
     * Check for Zend Optimizer+
     *
     * @link http://www.zend.com/products/server
     * @return bool
     */
    public static function hasZend()
    {
        return (bool) strlen(ini_get('zend_optimizer.enable_loader'));
    }

    /**
     * Check for nuSphere's phpExpress
     *
     * @link http://www.nusphere.com/products/phpexpress.htm
     * @return bool
     */
    public static function hasNusphere()
    {
        return Koch_SystemInfo::isLoadedExtension('phpexpress');
    }
}
