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
     * @param string $name            Name of instance.
     * @param type $classOrResolver Class or Closure (resolver).
     */
    public static function set($name, $classOrResolver)
    {
        static::$registry[$name] = $classOrResolver;
    }

    /**
     * Isset check, if instance,classname or resolver is set to registry.
     *
     * @param  string $name Name of instance.
     * @return bool
     */
    public static function has($name)
    {
        return isset(static::$registry[$name]);
    }

    /**
     * Getter for instance
     *
     * @param  string                      $name       Name of instance.
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
