<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Form\Decorators\Formelement;

use Koch\Form\AbstractFormElementDecorator;

/**
 * Formelement Decorator Description.
 *
 * Adds a <span> element containing the formelement
 * description after html_fromelement_content.
 */
class Description extends AbstractFormElementDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'description';

    public $cssClass = 'formdescription';

    /**
     * Renders description *after* formelement.
     */
    public function render($html_formelement)
    {
        if (isset($this->formelement->description)) {
            $html_formelement .= '<br />' . CR;
            $html_formelement .= '<span class="' . $this->cssClass . '">';
            $html_formelement .= $this->formelement->getDescription();
            $html_formelement .= '</span>' . CR;
        }

        return $html_formelement;
    }
}
