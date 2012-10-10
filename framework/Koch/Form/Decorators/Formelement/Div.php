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

namespace Koch\Form\Decorators\Formelement;

use Koch\Form\FormElementDecorator;

/**
 * Formelement_Decorator_Div
 *
 * Wraps a <div> element around the html_formelement_content.
 *
 * @category Koch
 * @package Koch_Form
 * @subpackage Koch_Form_Decorator
 */
class Div extends FormelementDecorator
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
