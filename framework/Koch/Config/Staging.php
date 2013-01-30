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

namespace Koch\Config;

/**
 * Class for Staging (Config overloading based on ServerName).
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
        $staging_config = \Koch\Config\Adapter\INI::readConfig(self::getFilename());

        // keys/values of array_to_overload are replaced with those of the staging_config
        return array_replace_recursive($array_to_overload, $staging_config);
    }

    /**
     * Getter for the staging config filename, which is determined by the servername
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
            case "localhost":
            case "intranet":
            case 'clansuite-dev.com':
            case 'www.clansuite-dev.com':
            case 'clansuite.dev':
                $filename = 'development.php';
                break;
            // staging configuration
            case 'clansuite-stage.com':
            case 'www.clansuite-stage.com':
            case 'clansuite.stage':
                $filename = 'staging.php';
                break;
            // intern configuration
            case 'clansuite-intern.com':
            case 'www.clansuite-intern.com':
            case 'clansuite.intern':
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
