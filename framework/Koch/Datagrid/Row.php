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
     * @var array Clansuite_Datagrid_Cell
     */
    private $_Cells = array();

    /**
     * The datagrid
     *
     * @var Clansuite_Datagrid $_Datagrid
     */
    private $_Datagrid;

    /**
     * The position of a column
     *
     * @var int
     */
    private $_Position = 0;

    //--------------------
    // Setter
    //--------------------

    /**
     * Set all row-cells
     *
     * @param array Clansuite_Datagrid_Cell
     */
    public function setCells($_Cells)
    {
        $this->_Cells = $_Cells;
    }

    /**
     * Set the position
     *
     * @param int
     */
    public function setPosition($_Position)
    {
        $this->_Position = $_Position;
    }

    //--------------------
    // Getter
    //--------------------

    /**
     * Get the cells for this row
     *
     * @return array Clansuite_Datagrid_Cell
     */
    public function getCells()
    {
        return $this->_Cells;
    }

    /**
     * Set the position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_Position;
    }

    //--------------------
    // Class methods
    //--------------------

    /**
     * Add a cell to the row
     *
     * @param Clansuite_Datagrid_Cell
     */
    public function addCell(&$_Cell)
    {
        // array_push($this->_Cells, $_Cell);
        $this->_Cells[] = $_Cell;
    }
}
