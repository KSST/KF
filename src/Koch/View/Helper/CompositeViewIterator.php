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

namespace Koch\View\Helper;

/**
 * CompositeView_Iterator.
 */
class CompositeViewIterator implements \ArrayAccess, \Countable, \Iterator
{
    public function __construct(private $composite)
    {
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
        if (isset($this->composite[$offset])) {
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
     * Return the current Iterator node element.
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
