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
 * Datagrid Column Renderer Link.
 *
 * Renders cells with a link (a href).
 */
class Link extends ColumnRenderer implements ColumnRendererInterface
{
    public $link            = '';
    public $linkFormat      = '&id=%{id}';
    public $linkId          = '';
    public $linkTitle       = '';
    public $nameWrapLength  = 50;
    public $nameFormat      = '%{name}';

    /**
     * Render the value(s) of a cell
     *
     * @param Koch\Datagrid\Datagrid\Cell
     * @return string Return html-code
     */
    public function renderCell($oCell)
    {
        // assign values to internal var
        $values = $oCell->getValues();

        // validate
        if (isset($values['name']) === false) {
            throw new Koch\Exception\Exception(_('A link needs a name. Please define "name" in the Result Keys.'));
        }

        if (mb_strlen($values['name']) > $this->nameWrapLength) {
            $values['name'] = mb_substr($values['name'], 0, $this->nameWrapLength - 3) . '...';
        }

        // set internal link
        $this->link = parent::getColumn()->getBaseURL();

        // render a href
        $options = array(
            'href' => Koch\Datagrid\Datagrid::appendUrl($this->linkFormat),
            'id' => $this->linkId,
            'title' => $this->linkTitle
        );
        $html_link = Koch\View\Helper\Html::renderElement('a', $this->nameFormat, $options);

        // replace
        return $this->replacePlaceholders($values, $html_link);
    }
}
