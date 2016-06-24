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

use Koch\Form\FormElementInterface;

class SubmitButton extends Input implements FormElementInterface
{
    public function __construct()
    {
        $this->type  = 'submit';
        $this->value = _('Submit');
        $this->class = 'SubmitButton ButtonGreen';
        $this->id    = 'SubmitButton';
        $this->name  = 'SubmitButton';
    }
}
