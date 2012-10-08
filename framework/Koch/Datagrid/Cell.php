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
 * Datagrid Cell
 *
 * Defines a cell within the datagrid
 */
class Cell extends Base
{
    /**
     * Value(s) of the cell
     * $_Values[0] is the standard value returned by getValue()
     *
     * @var array Mixed values
    */
    private $Values = array();

    /**
     * Column object (Clansuite_Datagrid_Column)
     *
     * @var object Clansuite_Datagrid_Column
     */
    private $columnObject;

    /**
     * Row object (Clansuite_Datagrid_Row)
     *
     * @var object Clansuite_Datagrid_Row
     */
    private $Row;

    /**
     * Set the column object of this cell
     *
     * @param Clansuite_Datagrid_Column $_columnObject
     */
    public function setColumnObject($_columnObject)
    {
        $this->columnObject = $_columnObject;
    }

    /**
     * Set the datagrid object
     *
     * @param Clansuite_Datagrid $_Datagrid
     */
    public function setjjjDatagrid($_Datagrid)
    {
        $this->_Datagrid = $_Datagrid;
    }

    /**
     * Set the row object of this cell
     *
     * @param Clansuite_Datagrid_Row $_Row
     */
    public function setRow($_Row)
    {
        $this->Row = $_Row;
    }

    /**
     * Set the value of the cell
     *
     * @param mixed A single value ($_Value[0])
     */
    public function setValue($_Value)
    {
        $this->Values[0] = $_Value;
    }

    /**
     * Set the values of the cell
     *
     * @param array Array of values
     */
    public function setValues($_Values)
    {
        $this->Values = $_Values;
    }

    /**
     * Returns the column object of this cell
     *
     * @return Clansuite_Datagrid_Column $_columnObject
     */
    public function getColumn()
    {
        return $this->columnObject;
    }

    /**
     * Get the Datagrid object
     *
     * @return Clansuite_Datagrid $_Datagrid
     */
    public function getDatagrid()
    {
        return $this->_Datagrid;
    }

    /**
     * Returns the row object of this cell
     *
     * @return Clansuite_Datagrid_Row $_Row
     */
    public function getRow()
    {
        return $this->Row;
    }

    /**
     * Returns the value of this cell
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->Values[0];
    }

    /**
     * Returns the values of this cell
     *
     * @return array
     */
    public function getValues()
    {
        return $this->Values;
    }

    /**
     * Render the value
     *
     * @return string Returns the value (maybe manipulated by column cell renderer)
     */
    public function render()
    {
        return $this->getColumn()->renderCell($this);
    }
}
