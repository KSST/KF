<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
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

        $filename = match ($_SERVER['SERVER_NAME']) {
            'localhost', 'intranet', 'application-dev.com', 'www.application-dev.com', 'application.dev' => 'development.php',
            'application-stage.com', 'www.application-stage.com', 'application.stage' => 'staging.php',
            'application-intern.com', 'www.application-intern.com', 'application.intern' => 'intern.php',
            default => 'production.php',
        };

        // return staging config filename
        return $filename;
    }

    public static function setFilename($filename)
    {
        self::$filename = $filename;
    }
}
