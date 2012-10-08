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
 * Datagrid Column Renderer Image
 *
 * Render cells with Image
 */
class Image extends ColumnRenderer implements ColumnRendererInterface
{
    public $nameWrapLength  = 25;

    /**
     * Render the value(s) of a cell
     *
     * @param Clansuite_Datagrid_Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        $image_alt = $value = $oCell->getValue();

        // build an image name for the alt-tag
        if ( mb_strlen($value) > $this->nameWrapLength ) {
            $image_alt = mb_substr($value, 0, $this->nameWrapLength - 5) . 'Image';
        }

        return $this->_replacePlaceholders($value, Clansuite_HTML::img($value, array( 'alt'  => $image_alt)));
    }
}
