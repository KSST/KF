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

class Tab extends AbstractFormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'tab';

    public function render($html_form_content)
    {
        return $html_form_content;
    }
}
