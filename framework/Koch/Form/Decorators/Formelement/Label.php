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
 * Formelement_Decorator_Label
 *
 * Adds a <label> element containing the formelement label in-front of html_fromelement_content.
 *
 * @category Koch
 * @package Koch_Form
 * @subpackage Koch_Form_Decorator
 */
class Label extends FormelementDecorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'label';

    /**
     * renders label BEFORE formelement
     *
     * @todo if required form field add (*)
     */
    public function render($html_formelement)
    {
        // add label
        if ( $this->formelement->hasLabel() == true) {
            // for attribute points to formelements id tag
            $html_formelement = CR . '<label for="'. $this->formelement->getId() .'">' . $this->formelement->getLabel() . '</label>'. CR . $html_formelement;
        }

        return $html_formelement;
    }
}
