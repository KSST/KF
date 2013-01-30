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

namespace Koch\Form\Validators;

use Koch\Form\Validator;

class String extends Validator
{
    public function getValidationHint()
    {
        return _('Please enter a string.');
    }

    public function getErrorMessage()
    {
        return _('The value must be a string.');
    }

    protected function processValidationLogic($value)
    {
        if (is_string($value) === true or is_numeric($value) === true) {
            return true;
        } else {
            return false;
        }
    }
}
