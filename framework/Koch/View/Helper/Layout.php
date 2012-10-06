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

namespace Koch\View\Helper;

/**
 * Koch Framework - Interface for all Nodes (Leaf-Objects)
 *
 * Each node (leaf-object) has to provide a method...
 */
interface Layout
{

    /**
     * Get the contents of this component in string form
     */
    public function render();

    public function __toString();

    /**
     * Set the data
     */
    public function setData(array $data); // array | assign placeholders 'data' = $data

    /**
     * Set the default content or to overwrite the leaf-content
     */
    public function setContent($content); // string | set content / placeholders 'data'

    /**
     * Set the default content
     */
    public function __construct($defaultContent);
}

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

/**
 * Koch Framework - Class for Layout Handling
 *
 * The Layout Object provides a document tree for the output elements.
 * Speaking in patterns: this is a "composite" view (GoF - German Edition - Page 239).
 * To get a better picture of the idea we speak of a tree/leaf(s) or parent/child(s) structure.
 * Every internal-node is-a leaf-node. At the end we render the whole tree.
 *
 * The Purpose is to divide / seperate all controller logic and view logic from each other.
 * Each controller (C) can add an element to the tree (V).
 * Doing this means, that we process all the controller logic before going on to the view logic.
 * First we get all controller's done, then we get all view's done.
 *
 * I know that there are some frameworks out there, which work with another approach,
 * where they switch back and forth between doing controller and doing view logic.
 * But i have decided not to implement it that way.
 *
 * @link http://koti.welho.com/pnikande/GoF-models/html/Composite.html
 * @link http://java.sun.com/blueprints/patterns/CompositeView.html
 * @link http://java.sun.com/blueprints/corej2eepatterns/Patterns/CompositeView.html
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Layout
 */
class ViewLayout implements ViewNodeInterface
{

    /**
     * Representation of the tree with leaf-nodes.
     *
     * @var array
     */
    private $components = array();

    /**
     * Adds / appends a new view-node (leaf-object) to the bottom of the stack
     */
    public function appendNode(ViewNodeInterface $component)
    {
        $this->components[] = $component;
    }

    /**
     * Fetches an iterator to traverse the nodes
     */
    public function getIterator()
    {
        $composite = new CompositeViewIterator($this->composite);
    }

    /**
     * Loops over all components / nodes and renders
     */
    public function render($response)
    {
        $subview = '';

        foreach ($this->components as $child) {
            $subview .= $child->render($response);
        }

        return $subview;
    }

}
