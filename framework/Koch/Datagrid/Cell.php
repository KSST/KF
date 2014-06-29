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
     * Column object (Koch\Datagrid\Datagrid\Column)
     *
     * @var object Koch\Datagrid\Datagrid\Column
     */
    private $columnObject;

    /**
     * Row object (Koch\Datagrid\Datagrid\Row)
     *
     * @var object Koch\Datagrid\Datagrid\Row
     */
    private $Row;

    /**
     * Set the column object of this cell
     *
     * @param Koch\Datagrid\Datagrid\Column $_columnObject
     */
    public function setColumnObject($_columnObject)
    {
        $this->columnObject = $_columnObject;
    }

    /**
     * Set the datagrid object
     *
     * @param Koch\Datagrid\Datagrid $_Datagrid
     */
    public function setjjjDatagrid($_Datagrid)
    {
        $this->_Datagrid = $_Datagrid;
    }

    /**
     * Set the row object of this cell
     *
     * @param Row $_Row
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
     * @return Koch\Datagrid\Datagrid\Column $_columnObject
     */
    public function getColumn()
    {
        return $this->columnObject;
    }

    /**
     * Get the Datagrid object
     *
     * @return Koch\Datagrid\Datagrid $_Datagrid
     */
    public function getDatagrid()
    {
        return $this->_Datagrid;
    }

    /**
     * Returns the row object of this cell
     *
     * @return Koch\Datagrid\Datagrid\Row $_Row
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
