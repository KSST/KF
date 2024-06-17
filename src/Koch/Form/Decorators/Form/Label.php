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

namespace Koch\Form\Decorators\Form;

use Koch\Form\AbstractFormDecorator;

class Label extends AbstractFormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'label';

    /**
     * renders label BEFORE formelement.
     */
    public function render($html_form_content)
    {
        echo 'some LABEL' . $html_form_content;
    }
}
