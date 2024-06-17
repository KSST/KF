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

namespace Koch\Session;

/**
 * Abstract base class for Session Storages.
 */
abstract class AbstractSession implements SessionInterface, \ArrayAccess
{
    /**
     * =======================
     *       Get and Set
     * =======================.
     */

    /**
     * Sets Data into the Session.
     *
     * @param string key
     * @param mixed  value
     */
    public function set($key, $value)
    {
        if (is_resource($value)) {
            throw new \LogicException('Do not store resources in the SESSION! Keep it light!');
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Gets Data from the Session.
     *
     * @param string key
     *
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
     * =====================================.
     */
    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($_SESSION[$offset])) {
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
