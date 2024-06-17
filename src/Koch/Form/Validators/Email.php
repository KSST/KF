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

namespace Koch\Form\Validators;

use Koch\Form\Validator;

class Email extends Validator
{
    public function getValidationHint()
    {
        return _('Please enter a valid email address.');
    }

    public function getErrorMessage()
    {
        return _('The value must be an email address.');
    }

    protected function processValidationLogic($value)
    {
        if (true === (bool) filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}
