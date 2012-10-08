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
 * Datagrid Column Renderer Email
 *
 * Renders cells with email (href mailto).
 */
class Email extends ColumnRenderer implements ColumnRendererInterface
{
    /**
     * Render the value(s) of a cell
     *
     * @param Clansuite_Datagrid_Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        $_Values = $oCell->getValues();

        if (isset($_Values[0]) AND isset($_Values[1])) {
            return sprintf('<a href="mailto:%s">%s</a>', $_Values[0], $_Values[1]);
        }

        if (isset($_Values[0])) {
            return sprintf('<a href="mailto:%s">%s</a>', $_Values[0], $_Values[0]);
        }
    }
}
