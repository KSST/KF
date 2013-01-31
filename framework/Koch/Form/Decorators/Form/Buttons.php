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

use Koch\Form\AbstractFormDecorator;

class Buttons extends AbstractFormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'buttons';

    public function render($html_form_content)
    {
        return $html_form_content;
    }
}
