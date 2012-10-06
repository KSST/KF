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

/**
 * Validates the value of an integer with minvalue given.
 */
class MinValue extends Validator
{
    public $minvalue;

    public function getMinValue()
    {
        return $this->minvalue;
    }

    /**
     * Setter for the minimum length of the string.
     *
     * @param int|float $minvalue
     */
    public function setMinValue($minvalue)
    {
        if (is_string($minvalue) === true) {
            $msg = _('Parameter Minvalue must be numeric (int|float) and not %s.');
            $msg = sprintf($msg, gettype($minvalue));

            throw new \InvalidArgumentException($msg);
        }

        $this->minvalue = $minvalue;
    }

    public function getErrorMessage()
    {
        $msg = _('The value deceeds (is less than) the minimum value of %s.');

        return sprintf($msg, $this->getMinValue());
    }

    public function getValidationHint()
    {
        $msg = _('Please enter a value not deceeding (being less than) the minimum value of %s.');

        return sprintf($msg, $this->getMinValue());
    }

    protected function processValidationLogic($value)
    {
        if ($value < $this->getMinValue()) {
            return false;
        }

        return true;
    }

}
