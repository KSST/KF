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

/**
 * Validates the value of an integer|float with maxvalue given.
 */
class MaxValue extends Validator
{
    public $maxvalue;

    public function getMaxValue()
    {
        return $this->maxvalue;
    }

    /**
     * Setter for the maximum length of the string.
     *
     * @param int|float $maxvalue
     */
    public function setMaxValue($maxvalue)
    {
        if (is_string($maxvalue)) {
            $msg = _('Parameter Maxvalue must be numeric (int|float) and not %s.');
            $msg = sprintf($msg, gettype($maxvalue));

            throw new \InvalidArgumentException($msg);
        }

        $this->maxvalue = $maxvalue;
    }

    public function getErrorMessage()
    {
        $msg = _('The value exceeds the maximum value of %s.');

        return sprintf($msg, $this->getMaxValue());
    }

    public function getValidationHint()
    {
        $msg = _('The value must be smaller than %s.');

        return sprintf($msg, $this->getMaxValue());
    }

    protected function processValidationLogic($value)
    {
        if ($value > $this->getMaxValue()) {
            return false;
        }

        return true;
    }
}
