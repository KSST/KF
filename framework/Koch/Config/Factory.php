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
 */

namespace Koch\Config;

/**
 * Koch Framework - A Factory for Configuration Adapters.
 *
 * The static method getConfiguration() includes and instantiates
 * a Configuration Engine Object and injects the configfile.
 */
class Factory
{
    /**
     * Instantiates the correct subclass determined by the fileextension
     *
     * Configuration Files must have one of the following extensions:
     *  .config.php
     *  .config.xml
     *  .config.yaml
     *  .info.php
     *
     * @param $configfile string path to configuration file
     * @return Cache Engine Object reads the configfile -> access to values via $config
     */
    public static function determineConfigurationHandlerTypeBy($configfile)
    {
        // init var
        $adapter = '';
        $extension = '';

        // use the filename only to detect adapter
        // @todo simplify extension detection, but watch out for .info.php
        #$extension = strtolower(pathinfo($configfile, PATHINFO_EXTENSION));

        $configfile = basename($configfile);
        preg_match('^(.config.php|.config.ini|.config.xml|.config.yaml|.info.php)$^', $configfile, $extension);
        if (empty($extension)) {
            throw new \Koch\Exception\Exception(_('Unknown file extension.'));
        }
        $extension = $extension[0];
        
        $extensionMap = array(
          '.config.php' => 'native',
          '.info.php' => 'native',
          '.config.ini' => 'ini',
          '.config.xml' => 'xml',
          '.config.yaml' => 'yaml'
        );

        if ($extensionMap[$extension] === true) {
            $adapter = $extensionMap[$extension];
        } else {
            throw new \Koch\Exception\Exception(
            _('No handler for that type of configuration file found (' . $extension . ')')
            );
        }

        return $adapter;
    }

    /**
     * Get Configuration
     *
     * Two in one method: determines the configuration handler automatically for a configfile.
     * Uses the confighandler to load the configfile and return the object.
     * The returned object contains the confighandler and the config array.
     *
     * @param $configfile Configuration file to load
     * @return Configuration Handler Object with confighandler and array of configfile.
     */
    public static function getConfiguration($configfile)
    {
        $handler = self::getHandler($configfile);

        return $handler::readConfig($configfile);
    }

    /**
     * Get Configuration Handler
     *
     * @param  string $configfile
     * @return object Configuration Handler Object
     */
    public static function getHandler($configfile)
    {
        $type = self::determineConfigurationHandlerTypeBy($configfile);

        $handler = self::getAdapter($type);

        return $handler;
    }

    /**
     * getAdapter()
     *
     * @param  string        $adapter a configuration filename type like "php", "xml", "yaml", "ini", "native"
     * @return Configuration Handler Object with confighandler and array of configfile.
     */
    public static function getAdapter($adapter)
    {
        $adapter = ($adapter == 'native') ? 'Native' : strtoupper($adapter);

        $class = 'Koch\Config\Adapter\\' . $adapter;

        return new $class;
    }
}
