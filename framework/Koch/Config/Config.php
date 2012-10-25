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
 * Provides a configuration container.
 *
 * This is a configuration container class.
 * It's build around the $config array, which is the storage container for settings.
 *
 * We use some php magic in here:
 * The array access implementation makes it seem that $config is an array,
 * even though it's an object! Why we do that? Because less to type!
 * The __set, __get, __isset, __unset are overloading functions to work with that array.
 *
 * Usage :
 * get data : $cfg->['name'] = 'john';
 * get data, using get() : echo $cfg->get ('name');
 * get data, using array access: echo $cfg['name'];
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Config
 */
class Config extends AbstractConfig
{
    /**
     * Setter for Config Array.
     * 
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Reads a configuration file
     *
     * @param  string $file
     * @return object $this->config
     */
    public function readConfig($file)
    {
        if (false === is_object($this->config)) {
            $this->config = Factory::getConfiguration($file);
        }

        return $this->config;
    }

    /**
     * Reads a configuration file of a module ($modulename . '.config.php')
     *
     * @param $module Name of Module
     * @return array Module Configuration Array
     */
    public function readModuleConfig($module = null)
    {
        // if no modulename is set, determine the name of the current module
        if ($module === null) {
            $module = \Koch\Router\TargetRoute::getModule();
        }

        $file = APPLICATION_MODULES_PATH . $module . DIRECTORY_SEPARATOR . $module . '.config.php';

        if (is_file($file)) {
            return Factory::getConfiguration($file);
        } else { // module has no configuration file

            return array();
        }
    }

    /**
     * Write module configuration file
     *
     * @param $array The configuration array to write.
     * @param $module The name of a module.
     */
    public function writeModuleConfig($array, $module = null)
    {
        if ($module === null) {
            $module = Koch\Router\TargetRoute::getModule();
        }

        $this->writeConfig(
            APPLICATION_MODULES_PATH . $module . DIRECTORY_SEPARATOR . $module . '.config.php',
            $array
        );
    }

    /**
     * Write a config file
     *
     * @param $file path and the filename you want to write
     * @param $array the configuration array to write. Defaults to null = empty array.
     */
    public function writeConfig($file, $array = array())
    {
        Factory::getHandler($file)->writeConfig($file, $array);
    }
}
