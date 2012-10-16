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

use Koch\Form\FormelementDecorator;

/**
 * Formelement_Decorator_Label
 *
 * Adds a <label> element containing the formelement label in-front of html_fromelement_content.
 *
 * @category Koch
 * @package Koch\Form
 * @subpackage Koch\Form\Decorators
 */
class Label extends FormelementDecorator
{
    /**
     * @var string Name of this decorator
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
        if ($this->formelement->hasLabel() == true) {
            // for attribute points to formelements id tag
            $html_formelement = CR . '<label for="' . $this->formelement->getId() . '">'
                . $this->formelement->getLabel()
                . '</label>' . CR . $html_formelement;
        }

        return $html_formelement;
    }
}
