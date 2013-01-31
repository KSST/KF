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

namespace Koch\Session;

/**
 * Koch Framework - Abstract base class for Session Storages.
 */
abstract class AbstractSession implements SessionInterface, \ArrayAccess
{
    /**
     * =======================
     *       Get and Set
     * =======================
     */

    /**
     * Sets Data into the Session.
     *
     * @param string key
     * @param mixed  value
     */
    public function set($key, $value)
    {
        if (is_resource($value) === true) {
            throw new \LogicException('Do not store resources in the SESSION! Keep it light!');
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Gets Data from the Session.
     *
     * @param string key
     * @return mixed value/boolean false
     */
    public function get($key)
    {
        if ($_SESSION[$key] !== null) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }

    /**
     * =====================================
     *   Implementation of SPL ArrayAccess
     * =====================================
     */

    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($_SESSION[$offset]) === true) {
            return $_SESSION[$offset];
        } else {
            throw new \InvalidArgumentException(sprintf('Array Key "%s" is not defined.', $offset));
        }
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    // @todo note by vain: check if this works on single array of session?
    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);

        return true;
    }
}
