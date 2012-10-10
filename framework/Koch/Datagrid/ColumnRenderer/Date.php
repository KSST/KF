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
 * Datagrid Column Renderer Date
 *
 * Renders date cells.
 */
class Date extends ColumnRenderer implements ColumnRendererInterface
{
    /**
     * Date format
     * Default: d.m.Y => 13.03.2007
     *
     * @todo make it respect the dateFormat setting from config
     *
     * @var string
     */
    public $dateFormat = 'd.m.Y H:i';

    /**
     * Render the value(s) of a cell
     *
     *
     * @param Koch\Datagrid\Datagrid\Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        $sDate = '';

        $oDatetime = date_create($oCell->getValue());

        if ($oDatetime !== false) {
            $sDate = $oDatetime->format($this->dateFormat);
        }

        return $sDate;
    }
}
