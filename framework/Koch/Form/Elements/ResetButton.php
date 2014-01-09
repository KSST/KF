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

namespace Koch\Form\Elements;

use Koch\Form\FormElementInterface;

class ResetButton extends Input implements FormElementInterface
{
    public function __construct()
    {
        $this->type = 'reset';
        $this->value = _('Reset');

        $this->class    = 'ResetButton ButtonGrey';
        $this->id       = 'ResetButton';
        $this->name     = 'ResetButton';
    }
}
