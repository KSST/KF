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
 * Class is a Container for Blocks.
 */
class Partial extends Layout
{
    // var $_blocks contains all block elements as Separate Objects
    private $blockObjects = array();

    // no constructor
    public function __construct()
    {

    }

    // add block object
    public function addBlock($name, Block $block)
    {
        $this->blockObjects[$name] = $block;
    }

    // execute each block in the container
    public function execute()
    {
        foreach ($this->blockObjects as $block) {
            $block->execute(); // $_blocks[] = $smarty->fetch("blockTemplate.tpl");
        }
    }

    /**
     * Render Blocks
     */
    public function render($params, $smarty)
    {
        // Set Smarty as View to each Block
        foreach ($this->blockObjects as $block) {
            $block->setView($smarty);
        }

        // Assign BlockObjects to Smarty
        $smarty->assign('block', $this->blockObjects);

        // Display it via /core/tools/sidebar.tpl
        // which loops over each Array Element (one block) and displays it
        $smarty->display('core/tools/sidebar.tpl');
    }

    /**
     * Getter for $this->_blockObjects
     */
    public function getBlocks()
    {
        return $this->blockObjects;
    }
}
