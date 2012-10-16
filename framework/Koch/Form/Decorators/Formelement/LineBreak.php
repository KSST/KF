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
 * Formelement_Decorator_None
 *
 * None - this hardly decorates anything at all.
 * Just wraps linebreaks around the html formelemnet content.
 *
 * @category Koch
 * @package Koch_Form
 * @subpackage Koch_Form_Decorator
 */
class LineBreak extends FormElementDecorator
{
    public $name = 'none';

    public function render($html_form_content)
    {
        // return $html_form_content;
        return CR . $html_form_content . CR;
    }
}
