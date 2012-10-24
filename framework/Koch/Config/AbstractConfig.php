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
 * Abstract Base Class for Config.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
abstract class AbstractConfig /*extends ArrayObject*/ implements \ArrayAccess
{
    /**
     * Configuration Array
     * protected = only visible to childs
     *
     * @var array(string==>mixed)
     */
    protected $config = array();

    /**
     * Returns $this->config Object as Array
     * On "unset = true" the array is returned and unset to save memory
     * and to avoid duplication of the config array.
     *
     * @param  boolean $unset If unset is true, $this->config array will be unset. Defaults to false.
     * @return config  array
     */
    public function toArray($unset = false)
    {
        $array = array();
        $array = $this->config;

        if ($unset === true) {
            unset($this->config);
        }

        return $array;
    }

    /**
     * Merges an array into the actual config array.
     *
     * @param array(string=>mixed) $newConfig The new config array.
     */
    public function merge(array $newConfig)
    {
        foreach ($newConfig as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Gets a Config Value or sets a default value
     *
     * @example
     * Usage for one default variable:
     * self::getConfigValue('items_newswidget', '8');
     * Gets the value for the key items_newswidget from the moduleconfig or sets the value to 8.
     *
     * Usage for two default variables:
     * self::getConfigValue('items_newswidget', $_GET['numberNews'], '8');
     * Gets the value for the key items_newswidget from the moduleconfig or sets the value
     * incomming via GET, if nothing is incomming, sets the default value of 8.
     *
     * @param string $keyname     The keyname to find in the array.
     * @param mixed  $default_one Default value. Returned, if the keyname was not found.
     * @param mixed  $default_two Default value. Returned, if the keyname was not found and default_one is null.
     */
    public function getConfigValue($keyname, $default_one = null, $default_two = null)
    {
        // try a lookup of the value by keyname
        $value = Clansuite_Functions::array_find_element_by_key($keyname, $this->config);

        // return value or default
        if (empty($value) === false) {
            return $value;
        } elseif ($default_one != null) {
            return $default_one;
        } elseif ($default_two != null) {
            return $default_two;
        } else {
            return null;
        }
    }

    /**
     * Gets a config file item based on keyname
     *
     * @param    string    the config item key
     * @return void
     */
    public function __get($configkey)
    {
        if ($this->config[$configkey] !== null) {
            return $this->config[$configkey];
        } else {
            return null;
        }
    }

    /**
     * Set a config file item based on key:value
     *
     * @param string the config item key
     * @param string the config item value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->config[$key] = $value;

        return true;
    }

    /**
     * Method allows 'isset' to work on $this->data
     *
     * @param  string $name Name of Variable Key $this->data[$name]
     * @return return mixed
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * Method allows 'unset' calls to work on $this->data
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->config[$key]);
    }

    /**
     * Implementation of SPL ArrayAccess
     */

    /**
     * ArrayAccess::offsetExists()
     *
     * @param  mixed $offset
     * @return mixed Clansuite_Config value
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * ArrayAccess::offsetGet()
     *
     * @param  mixed $offset
     * @return mixed Clansuite_Config value
     */
    public function offsetGet($offset)
    {
        if (true === isset($this->config[$offset])) {
            return $this->config[$offset];
        } else {
            /*if (defined('DEBUG') === true and DEBUG === true) {
                throw new \InvalidArgumentException(sprintf('Array Key "%s" is not defined.', $offset));
            } else {*/

                return false;
            //}
        }
    }

    /**
     * ArrayAccess::offsetSet()
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset) === true) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    /**
     * ArrayAccess::offsetUnset()
     *
     * @param  mixed $offset
     * @return bool  true
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);

        return true;
    }
}
