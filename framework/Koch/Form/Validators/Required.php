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

class Required extends Validator
{
    public function getValidationHint()
    {
        return _('Please fill the formfield. The value must not be empty.');
    }

    public function getErrorMessage()
    {
        return _('The value is required.');
    }

    protected function processValidationLogic($value)
    {
        if ($value !== null and ('' !== $value)) {
            return true;
        } else {
            return false;
        }
    }
}
