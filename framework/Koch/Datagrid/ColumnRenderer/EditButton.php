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

use Koch\Datagrid\ColumnRenderer;
use Koch\Form\Elements\ImageButton;

/**
 * Datagrid Column Renderer for Edit Button Cells
 *
 * Renders an edit button.
 */
class EditButton extends ColumnRenderer implements ColumnRendererInterface
{
    /**
    * Render the value(s) of a cell
    *
    * @param Koch\Datagrid\Datagrid\Cell
    * @return string Return html-code
    */
    public function renderCell($oCell)
    {
        $oImagebutton = new ImageButton();
        $oImagebutton->setName('Editbutton');
        $oImagebutton->setID('Editbutton-' . $oCell->getValue());
        $oImagebutton->setClass('DatagridEditbutton-' . $oCell->getColumn()->getAlias());
        $oImagebutton->setValue(_('Edit'));

        return $oImagebutton->render();

        /*

        return sprintf(
            '<input type="button" value="EditButton" id="EditButton-%s" name="EditButton" />',
            $oCell->getValue()
        );
        */
    }
}
