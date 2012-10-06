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
 * Formelement Decorator Description.
 *
 * Adds a <span> element containing the formelement
 * description after html_fromelement_content.
 *
 * @category Koch
 * @package Koch_Form
 * @subpackage Koch_Form_Decorator
 */
class Description extends FormelementDecorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'description';

    /**
     * renders description AFTER formelement
     *
     * @todo if required form field add (*)
     */
    public function render($html_formelement)
    {
        // add description
        if ( isset($this->formelement->description) == true) {
            $html_formelement .= '<br />'. CR;
            $html_formelement .= '<span class="formdescription">' . $this->formelement->getDescription() . '</span>' . CR;
        }

        return $html_formelement;
    }
}
