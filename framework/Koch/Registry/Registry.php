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

namespace Koch\Registry;

/**
 * Koch Framework - Registry
 */
class Registry
{
    public static $registry = array();

    /**
     * Set instance to registry.
     *
     * @param type $name            Name of instance.
     * @param type $classOrResolver Class or Closure (resolver).
     */
    public static function set($name, $classOrResolver)
    {
        static::$registry[$name] = $classOrResolver;
    }

    /**
     * Isset check, if instance,classname or resolver is set to registry.
     *
     * @param  type $name Name of instance.
     * @return bool
     */
    public static function has($name)
    {
        return isset(static::$registry[$name]);
    }

    /**
     * Getter for instance
     *
     * @param  type                      $name       Name of instance.
     * @param  type                      $parameters
     * @return object
     * @throws \InvalidArgumentException
     */
    public static function get($name, $parameters = null)
    {
        if (false === isset(static::$registry[$name])) {
            throw new \InvalidArgumentException(
                _('No resolver found for "' . $name . '". Register a resolver.')
            );
        }

        // execute the closure (as a resolver) for the instance
        if (static::$registry[$name] instanceof \Closure) {
            static::$registry[$name] = call_user_func(static::$registry[$name], $parameters);
        }

        // instantiate the className
        if (true === is_string(static::$registry[$name])) {
            $class = static::$registry[$name];

            return new $class($parameters);
        }

        return static::$registry[$name];
    }
}
