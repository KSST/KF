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
 * Koch Framework - Provides a configuration container.
 *
 * This is a configuration container class.
 * It's build around the $config array, which is the storage container for settings.
 *
 * We use some php magic in here:
 * The array access implementation makes it seem that $config is an array,
 * even though it's an object! Why we do that? Because less to type!
 * The __set, __get, __isset, __unset are overloading functions to work with that array.
 */
class Config extends AbstractConfig
{
    /**
     * Reads a configuration file.
     *
     * @param string $file
     *
     * @return object $this->config
     */
    public function read($file)
    {
        if (false === is_object($this->config)) {
            $this->config = Factory::getConfiguration($file);
        }

        return $this->config;
    }

    /**
     * Write a config file.
     *
     * @param string $file path and the filename you want to write
     * @param $array the configuration array to write. Defaults to null = empty array.
     *
     * @return bool
     */
    public function write($file, $array = [])
    {
        return Factory::getHandler($file)->write($file, $array);
    }

    /**
     * Fetches the Application Main Config from file or APC.
     *
     * @return type
     */
    public function getApplicationConfig()
    {
        $config = [];

        $apcAppKey = APPLICATION_NAME . '.config';

        // load config from APC
        if (APC && apc_exists($apcAppKey)) {
            $config = apc_fetch($apcAppKey);
        } else {
            // load config from file
            $config = \Koch\Config\Adapter\INI::read(
                APPLICATION_PATH . 'Configuration/' . APPLICATION_NAME . '.php'
            );
            // set to APC
            if (APC) {
                apc_add($apcAppKey, $config);
            }
        }

        unset($apcAppKey);

        // merge config with a staging configuration
        if (isset($config['config']['staging']) && (bool) $config['config']['staging']) {
            $config = \Koch\Config\Staging::overloadWithStagingConfig($config);
        }

        return $config;
    }

    /**
     * Reads a configuration file of a module ($modulename . '.config.php').
     *
     * @param string $module Name of Module
     *
     * @return array Module Configuration Array
     */
    public function readModuleConfig($module = null)
    {
        // if no modulename is set, determine the name of the current module
        $module = ($module === null) ? \Koch\Router\TargetRoute::getModule() : ucfirst($module);

        $file = APPLICATION_MODULES_PATH . $module . DIRECTORY_SEPARATOR . $module . '.config.php';

        $result = is_file($file) ? Factory::getConfiguration($file) : [];

        return $result;
    }

    /**
     * Write module configuration file.
     *
     * @param $array The configuration array to write.
     * @param string $module The name of a module.
     *
     * @return bool
     */
    public function writeModuleConfig(array $array, $module = null)
    {
        // if no modulename is set, determine the name of the current module
        $module = ($module === null) ? \Koch\Router\TargetRoute::getModule() : ucfirst($module);

        $file = realpath(APPLICATION_MODULES_PATH . $module . DIRECTORY_SEPARATOR . $module . '.config.php');

        return $this->write($file, $array);
    }
}
