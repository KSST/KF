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
     * @param Koch\Datagrid\Datagrid\Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        $checkbox = new \Koch\Form\Elements\Checkbox();
        $checkbox->setName('Checkbox[]');
        $checkbox->setID('Checkbox-' . $oCell->getValue());
        $checkbox->setValue($oCell->getValue());
        $checkbox->setClass('DatagridCheckbox DatagridCheckbox-' . $oCell->getColumn()->getAlias());

        return $checkbox->render();

        /*

        return sprintf(
            '<input type="checkbox" value="%s" id="Checkbox-%s" name="Checkbox[]" />',
            $oCell->getValue(),
            $oCell->getValue()
        );
        */
    }
}
