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

class Tab extends FormDecorator
{
    /**
     * Name of this decorator
     *
     * @var string
     */
    public $name = 'tab';

    public function render($html_form_content)
    {
        return $html_form_content;
    }
}
