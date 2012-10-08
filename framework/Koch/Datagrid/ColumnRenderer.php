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
 * Datagrid ColumnRenderer
 *
 * Provides standard methods for all Datagrid Column Renderers.
 */
class ColumnRenderer extends Renderer
{
    /**
     * The column object
     *
     * @var object Clansuite_Datagrid_Column
     */
    private $column;

    //---------------------
    // Setter
    //---------------------

    /**
     * Set the col object
     *
     * @param Clansuite_Datagrid_Column $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    //---------------------
    // Getter
    //---------------------

    /**
     * Get the column object
     *
     * @return Clansuite_Datagrid_Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Instantiate the Column Base
     *
     * @param Clansuite_Datagrid_Column
     */
    public function __construct($column)
    {
        $this->setColumn($column);
    }

    //---------------------
    // Class methods
    //---------------------

    /**
     * Replace placeholders with values
     *
     * @param  array  $values
     * @param  string $format
     * @return string
     */
    public function _replacePlaceholders($values, $format)
    {
        $placeholders   = array();
        $replacements   = array();

        // search for placeholders %{...}
        preg_match_all('#%\{([^\}]+)\}#', $format, $placeholders, PREG_PATTERN_ORDER );

        // check if placeholders are used
        // @todo replace count() with check for first placeholder element: if($_Placeholders[1][0] !== null)
        //       and move count into the if
        $_PlacerholderCount = count($placeholders[1]);
        if ($_PlacerholderCount > 0) {
            // loop over placeholders
            for ($i=0;$i<$_PlacerholderCount;$i++) {
                if ( isset($values[$placeholders[1][$i]]) ) {
                    $replacements['%{' . $placeholders[1][$i] . '}'] = $values[$placeholders[1][$i]];
                }
            }
        }

        // return substituted string
        return strtr($format, $replacements);
    }
}
