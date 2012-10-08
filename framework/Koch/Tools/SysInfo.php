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
