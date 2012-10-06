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

namespace Koch\Datagrid\ColumnRenderer;

/**
 * Datagrid Column Renderer String.
 *
 * Renders cells with string type value.
 */
class String extends ColumnRenderer implements ColumnRendererInterface
{
    public $stringFormat = '';

    /**
     * Render the value(s) of a cell
     *
     * @param Clansuite_Datagrid_Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        if ($this->stringFormat == '') {
            return $oCell->getValue();
        } else {
            return $this->_replacePlaceholders($oCell->getValues(), $this->stringFormat);
        }
    }
}
