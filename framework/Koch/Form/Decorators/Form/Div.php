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

namespace Koch\Form\Decorators\Form;

use Koch\Form\FormDecorator;

/**
 * Form_Decorator_Div
 *
 * Wraps a <div> element around the html_form_content.
 *
 * @category Koch
 * @package Koch_Form
 * @subpackage Koch_Form_Decorator
 */
class Div extends FormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'div';

    public function render($html_form_content)
    {
        // open opening div tag (unclosed first tag)
        $html_deco = CR . '<div ';

        // add class
        if ( mb_strlen($this->getClass()) > 0 ) {
             $html_deco .= 'class="' . $this->getClass() .'" ';
        }

        // add class
        if ( mb_strlen($this->getId()) > 0 ) {
             $html_deco .= 'id="' . $this->getId() .'" ';
        }

        // close opening div tag (close unclosed first tag)
        $html_deco .= '>';

        return  $html_deco . $html_form_content . '</div>' . CR;
    }
}
