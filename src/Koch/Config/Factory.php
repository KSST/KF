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
 * A Factory for Configuration Adapters.
 *
 * The static method getConfiguration() includes and instantiates
 * a Configuration Engine Object and injects the configfile.
 */
class Factory
{
    /**
     * Instantiates the correct subclass determined by the fileextension.
     *
     * Configuration Files must have one of the following extensions:
     *  .config.php
     *  .config.xml
     *  .config.yaml
     *  .info.php
     *
     * @param string $configfile string path to configuration file
     *
     * @return Cache Engine Object reads the configfile -> access to values via $config
     */
    public static function determineConfigurationHandlerTypeBy($configfile)
    {
        // init var
        $adapter   = '';
        $extension = '';

        // use the filename only to detect adapter
        // @todo simplify extension detection, but watch out for .info.php
        #$extension = strtolower(pathinfo($configfile, PATHINFO_EXTENSION));

        $configfile = basename($configfile);
        preg_match('^(.config.php|.config.ini|.config.xml|.config.yaml|.info.php)$^', $configfile, $extension);
        if (empty($extension)) {
            throw new \Koch\Exception\Exception('Unknown file extension.');
        }
        $extension = $extension[0];

        static $extensionMap = [
            '.config.php'  => 'PHP',
            '.info.php'    => 'PHP',
            '.config.ini'  => 'INI',
            '.config.xml'  => 'XML',
            '.config.yaml' => 'YAML',
        ];

        if (isset($extensionMap[$extension])) {
            $adapter = $extensionMap[$extension];
        } else {
            throw new \Koch\Exception\Exception(
                _('No handler for that type of configuration file found (' . $extension . ')')
            );
        }

        return $adapter;
    }

    /**
     * Get Configuration.
     *
     * Two in one method: determines the configuration handler automatically for a configfile.
     * Uses the confighandler to load the configfile and return the object.
     * The returned object contains the confighandler and the config array.
     *
     * @param string $configfile Configuration file to load
     *
     * @return Configuration Handler Object with confighandler and array of configfile.
     */
    public static function getConfiguration($configfile)
    {
        $handler = self::getHandler($configfile);

        return $handler::read($configfile);
    }

    /**
     * Get Configuration Handler.
     *
     * @param string $configfile
     *
     * @return object Configuration Handler Object
     */
    public static function getHandler($configfile)
    {
        $type = self::determineConfigurationHandlerTypeBy($configfile);

        $handler = self::getAdapter($type);

        return $handler;
    }

    /**
     * getAdapter().
     *
     * @param string $adapter A config adapter name "PHP", "XML", "YAML", "INI", "JSON".
     *
     * @return object /Koch/Config/Adapter/AdapterInterface
     */
    public static function getAdapter($adapter)
    {
        $class = 'Koch\Config\Adapter\\' . $adapter;

        return new $class();
    }
}
