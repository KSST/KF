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

class Label extends FormDecorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'label';

    /**
     * renders label BEFORE formelement
     */
    public function render($html_form_content)
    {
           echo 'some LABEL' . $html_form_content;
    }
}
