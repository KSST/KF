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
 * Datagrid Column
 *
 * Defines a column of the datagrid
 */
class Column extends Base
{
    //--------------------
    // Class properties
    //--------------------

    /**
     * All cells of this column (array of references)
     *
     * @var array Koch\Datagrid\Datagrid_Cell
     */
    private $cells = array();

    /**
     * The sortmode of the column
     *
     * @var string
     */
    private $sortOrder = 'DESC';

    /**
     * The sortfield of the column
     *
     * @var string
     */
    private $sortField = '';

    /**
     * The position of a column
     *
     * @var int
     */
    private $position = 0;

    /**
     * Renderer for the cell
     *
     * @var object Koch\Datagrid\Datagrid_Column_Renderer
     */
    private $renderer;

    /**
     * Boolean datagrid column values for configuration, wrapped into an array
     *
     * @var array
     */
    private $features = array(
        'Sorting'       => true,
        'Search'        => true
    );

    //--------------------
    // Setter
    //--------------------

    /**
     * Set all row-cells
     *
     * @param array Koch\Datagrid\Datagrid_Cell
     */
    public function setCells($_Cells)
    {
        $this->cells = $_Cells;
    }

    /**
     * Set the position
     *
     * @param int
     */
    public function setPosition($_Position)
    {
        $this->position = $_Position;
    }

    /**
     * Set the renderer for the column
     *
     * @param mixed string|object Renderer Name|Koch\Datagrid\Datagrid_Column_Renderer
     */
    public function setRenderer($_Renderer)
    {
        if ($_Renderer instanceof Koch\Datagrid\Datagrid_Column_Renderer_Base) {
            $this->renderer = $_Renderer;
        } else {
            $this->renderer = $this->loadColumnRenderer($_Renderer);
        }
    }

    /**
     * Set the database sortfield
     *
     * @param string
     */
    public function setSortField($_sortField)
    {
        $this->sortField = $_sortField;
    }

    /**
     * Sets the default sorting method for this column
     *
     * @param string The default sorting method, possible values are: ASC, DESC, NATASC, NETDESC.
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    //--------------------
    // Getter
    //--------------------

    /**
     * Get the position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get the renderer for the column
     *
     * @return Koch\Datagrid\Datagrid_Column_Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Get the sort-field
     *
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * Get the sort-mode
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    //--------------------
    // Class methods
    //--------------------

    /**
     * Check for datagrid column features
     *
     * @see $this->_features
     * @param  string  $feature
     * @return boolean
     */
    public function isEnabled($feature)
    {
        if ( !isset($this->features[$feature]) ) {
            throw new Koch\Exception\Exception(_('There is no such feature in this datagrid column: ') . $feature);
        } else {
            return $this->features[$feature];
        }
    }

    /**
     * Enable datagrid column features and return true if it succeeded, false if not
     *
     * @see $this->_features
     * @param  string  $feature
     * @return boolean
     */
    public function enableFeature($feature)
    {
        if ( false == isset($this->features[$feature]) ) {
            return false;
        } else {
            $this->features[$feature] = true;

            return true;
        }
    }

    /**
     * Disable datagrid column features
     * Return true if succeeded, false if not
     *
     * @see $this->_features
     * @param  mixed   $feature
     * @return boolean
     */
    public function disableFeature($feature)
    {
        if ( false == isset($this->features[$feature]) ) {
            return false;
        } else {
            $this->features[$feature] = false;

            return true;
        }
    }

    /**
     * Add a cell reference to the col
     *
     * @param Koch\Datagrid\Datagrid_Cell
     */
    public function addCell($cell)
    {
        array_push($this->cells, $cell);
    }

    /**
     * Load the renderer depending on a string (lowercased)
     * The method uses the folder "datagrid/columns" and loads [$name].php
     *
     * @param string $rendererName The renderer name, e.g. Link, EditButton.
     */
    private function loadColumnRenderer($rendererName = '')
    {
        // name to classname
        $rendererName = ucfirst($rendererName);
        // special case: camelCase on EditButton
        $rendererName = ($rendererName == 'Editbutton') ? 'EditButton' : '';

        $className = 'Koch\Datagrid\ColumnRenderer\\' . $rendererName;

        if (false == class_exists($className, false)) {
            $file = __DIR__ . '/ColumnRenderer/' . $rendererName . '.php';

            if ( is_file($file) ) {
                include $file;

                if (false == class_exists($className, false)) {
                    throw new Koch\Exception\Exception(_('The column renderer class does not exist: ') . $className);
                }
            } else {
                throw new Koch\Exception\Exception(_('The column renderer file does not exist: ') . $file);
            }
        }

        return new $className($this);
    }

    /**
     * Renders the column cell depanding on the renderer that is assigned to the column object
     * Default renderer: String
     *
     * @return string Returns html-code
     *                @param Koch\Datagrid\Datagrid_Cell
     */
    public function renderCell($oCell)
    {
        return $this->getRenderer()->renderCell($oCell);
    }
}
