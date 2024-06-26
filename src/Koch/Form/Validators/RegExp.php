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

class RegExp extends Validator
{
    /**
     * The regular expression to be used on the value.
     *
     * @var string
     */
    private $regexp;

    /**
     * Sets the Regular Expression to use in the value.
     *
     * @param string $regexp The regular expression
     */
    public function setRegexp($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * Returns the Regular Expression.
     *
     * @return string the regular expression
     */
    public function getRegexp()
    {
        return $this->regexp;
    }

    public function getValidationHint()
    {
        return _('The values must match the following criteria.');
    }

    public function getErrorMessage()
    {
        return _('The values must match the following criteria.');
    }

    protected function processValidationLogic($value)
    {
        if (true === preg_match($this->regexp, (string) $value)) {
            return true;
        } else {
            return false;
        }
    }
}
