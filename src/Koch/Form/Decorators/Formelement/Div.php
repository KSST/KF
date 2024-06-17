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
 * Formelement_Decorator_Div.
 *
 * Wraps a <div> element around the html_formelement_content.
 */
class Div extends AbstractFormElementDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'div';

    public function render($html_formelement_content)
    {
        return CR . '<div class="' . $this->getClass() . '">' . $html_formelement_content . '</div>' . CR;
    }
}
