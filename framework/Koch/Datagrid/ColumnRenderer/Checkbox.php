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
 * Datagrid Column Renderer Checkbox
 *
 * Renders cell with a checkbox
 */
class Checkbox extends ColumnRenderer implements ColumnRendererInterface
{
    /**
     * Render the value(s) of a cell
     *
     * @param Clansuite_Datagrid_Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        $oCheckbox = new Clansuite_Formelement_Checkbox();
        $oCheckbox->setName('Checkbox[]');
        $oCheckbox->setID('Checkbox-' . $oCell->getValue());
        $oCheckbox->setValue($oCell->getValue());
        $oCheckbox->setClass('DatagridCheckbox DatagridCheckbox-' . $oCell->getColumn()->getAlias());

        return $oCheckbox->render();

        /*

        return sprintf(
            '<input type="checkbox" value="%s" id="Checkbox-%s" name="Checkbox[]" />',
            $oCell->getValue(),
            $oCell->getValue()
        );
        */
    }
}
