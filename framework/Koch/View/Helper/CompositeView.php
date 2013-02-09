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
