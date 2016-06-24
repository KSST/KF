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

namespace Koch\Form\Elements;

use Koch\Form\FormElement;
use Koch\Form\FormElementInterface;

/**
 * Formelement_Timetoken.
 */
class TimeToken extends FormElement implements FormElementInterface
{
    public function generateToken()
    {
        // @todo consider using PHP Spam Kit Class
    }

    /**
     * Inserts a hidden input field for a token. Reducing the risk of an CSRF exploit.
     */
    public function render()
    {
        return '<input type="hidden" name="' . $this->generateToken() . '" value="1" />';
    }
}
