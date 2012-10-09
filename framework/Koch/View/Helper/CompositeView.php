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
class CompositeView implements ViewNodeInterface
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
