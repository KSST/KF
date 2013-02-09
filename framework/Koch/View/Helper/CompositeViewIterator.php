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

namespace Koch\View\Helper;

/**
 * CompositeView_Iterator
 */
class CompositeViewIterator implements \ArrayAccess, \Countable, \Iterator
{

    private $composite = array();

    public function __construct($composite)
    {
        $this->composite = $composite;
    }

    /**
     * Implementation of {@see ArrayAccess::offsetExists()}.
     *
     * @return
     */
    public function offsetExists($offset)
    {
        return isset($this->composite[$offset]);
    }

    /**
     * Gets a node from the composite.
     *
     * Implementation of {@see ArrayAccess::offsetGet()}.
     *
     * @return
     */
    public function offsetGet($offset)
    {
        if (isset($this->composite[$offset]) === true) {
            return $this->composite[$offset];
        } else {
            throw new \InvalidArgumentException(sprintf('Array Key "%s" is not defined.', $offset));
        }
    }

    /**
     * Sets a node to the composite.
     *
     * Implementation of {@see ArrayAccess::offsetSet()}.
     *
     * @return
     */
    public function offsetSet($offset, $value)
    {
        return $this->composite[$offset] = $value;
    }

    /**
     * Unsets a composite node.
     *
     * Implementation of {@see ArrayAccess::offsetUnset()}.
     *
     * @return
     */
    public function offsetUnset($offset)
    {
        unset($this->composite[$offset]);
    }

    /**
     * Returns the number of nodes.
     *
     * Implementation of {@see Countable::count()}.
     */
    public function count()
    {
        return count($this->composite);
    }

    /**
     * Return the current Iterator node element
     *
     * Implementation of {@see Iterator::current()}.
     *
     * @return mixed Current node
     */
    public function current()
    {
        $key = key($this->composite);

        return $this->offsetGet($key);
    }

    /**
     * Go to the next Node.
     *
     * Implementation of {@see Iterator::next()}.
     *
     * @return
     */
    public function next()
    {
        next($this->composite);
    }

    /**
     * Returns the current node key.
     *
     * Implementation of {@see Iterator::key()}.
     *
     * @return int Key
     */
    public function key()
    {
        return key($this->composite);
    }

    /**
     * Check if current Node position is valid.
     *
     * Implementation of {@see Iterator::valid()}.
     *
     * @return bool Is valid
     */
    public function valid()
    {
        return false !== current($this->composite);
    }

    /**
     * Resets the Iterator to the first element.
     *
     * Implementation of {@see Iterator::rewind()}.
     *
     * @return
     */
    public function rewind()
    {
        reset($this->composite);
    }
}
