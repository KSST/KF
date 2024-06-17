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
 * Validates a integer to be in a certain range.
 */
class Range extends Validator
{
    /**
     * @var filter var options
     */
    public $options = [];

    /**
     * Setter for the range array.
     *
     * @param int $minimum_length The minimum length of the string.
     * @param int $maximum_length The maximum length of the string.
     */
    public function setRange($minimum_length, $maximum_length)
    {
        $this->options['options']['min_range'] = (int) $minimum_length;
        $this->options['options']['max_range'] = (int) $maximum_length;
    }

    public function getValidationHint()
    {
        $min = $this->options['options']['min_range'];
        $max = $this->options['options']['max_range'];

        $msg = _('Please enter a value within the range of %s <> %s.');

        return sprintf($msg, $min, $max);
    }

    public function getErrorMessage()
    {
        $min = $this->options['options']['min_range'];
        $max = $this->options['options']['max_range'];

        $msg = _('The value is outside the range of %s <> %s.');

        return sprintf($msg, $min, $max);
    }

    protected function processValidationLogic($value)
    {
        if (false !== filter_var($value, FILTER_VALIDATE_INT, $this->options)) {
            return true;
        } else {
            return false;
        }
    }
}
