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

class Errors extends AbstractFormDecorator
{
    /**
     * @var string Name of this decorator
     */
    public $name = 'errors';

    /**
     * @param string $html_form_content
     */
    public function render($html_form_content)
    {
        $errors = '<ul id="form-errors">';

        $messages = $this->getErrorMessages();

        foreach ($messages as $idx => $message) {
            $errors .= '<li>' . $message . '</li>';
        }

        $errors .= '</ul>';

        return $errors . $html_form_content;
    }
}
