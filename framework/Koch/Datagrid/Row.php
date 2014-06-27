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

namespace Koch\Datagrid;

/**
 * Datagrid Row
 *
 * Defines one single row for the datagrid
 */
class Row extends Base
{
    //--------------------
    // Class properties
    //--------------------

    /**
     * All cells of this row
     *
     * @var array Koch\Datagrid\Datagrid\Cell
     */
    private $Cells = array();

    /**
     * The position of a column
     *
     * @var int
     */
    private $Position = 0;

    //--------------------
    // Setter
    //--------------------

    /**
     * Set all row-cells
     *
     * @param array Koch\Datagrid\Datagrid\Cell
     */
    public function setCells($_Cells)
    {
        $this->Cells = $_Cells;
    }

    /**
     * Set the position
     *
     * @param int
     */
    public function setPosition($_Position)
    {
        $this->Position = $_Position;
    }

    //--------------------
    // Getter
    //--------------------

    /**
     * Get the cells for this row
     *
     * @return array Koch\Datagrid\Datagrid\Cell
     */
    public function getCells()
    {
        return $this->Cells;
    }

    /**
     * Set the position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->Position;
    }

    //--------------------
    // Class methods
    //--------------------

    /**
     * Add a cell to the row
     *
     * @param Koch\Datagrid\Datagrid\Cell
     * @param Cell $_Cell
     */
    public function addCell(&$_Cell)
    {
        // array_push($this->_Cells, $_Cell);
        $this->Cells[] = $_Cell;
    }
}
