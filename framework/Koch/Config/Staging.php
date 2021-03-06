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

namespace Koch\Config;

/**
 * Koch Framework - Class for Staging (Config overloading based on ServerName).
 */
class Staging
{
    /**
     * This is the configuration file, which is overloaded.
     *
     * @var string
     */
    private static $filename = '';

    /**
     * Loads a staging configuration file and overloads the given array.
     *
     * @param array the array to overload
     *
     * @return array Merged configuration.
     */
    public static function overloadWithStagingConfig($array_to_overload)
    {
        // load staging config
        $staging_config = \Koch\Config\Adapter\INI::read(self::getFilename());

        // keys/values of array_to_overload are replaced with those of the staging_config
        return array_replace_recursive($array_to_overload, $staging_config);
    }

    /**
     * Getter for the staging config filename, which is determined by the servername.
     *
     * @return string filename of staging config
     */
    public static function getFilename()
    {
        if (isset(self::$filename)) {
            return self::$filename;
        }

        $filename = '';

        switch ($_SERVER['SERVER_NAME']) {
            // development configuration
            case 'localhost':
            case 'intranet':
            case 'application-dev.com':
            case 'www.application-dev.com':
            case 'application.dev':
                $filename = 'development.php';
                break;
            // staging configuration
            case 'application-stage.com':
            case 'www.application-stage.com':
            case 'application.stage':
                $filename = 'staging.php';
                break;
            // intern configuration
            case 'application-intern.com':
            case 'www.application-intern.com':
            case 'application.intern':
                $filename = 'intern.php';
                break;
            default:
                $filename = 'production.php';
        }

        // return staging config filename
        return $filename;
    }

    public static function setFilename($filename)
    {
        self::$filename = $filename;
    }
}
