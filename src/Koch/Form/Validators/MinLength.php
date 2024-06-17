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
 * Validates the lenght of a string with maxlength given.
 */
class MinLength extends Validator
{
    public $minlength;

    public function getMinlength()
    {
        return $this->minlength;
    }

    /**
     * Setter for the minimum length of the string.
     *
     * @param int $minlength
     */
    public function setMinlength($minlength)
    {
        $this->minlength = (int) $minlength;
    }

    public function getErrorMessage()
    {
        $msg = _('The value is less than the Minlength of %s chars.');

        return sprintf($msg, $this->getMinlength());
    }

    public function getValidationHint()
    {
        $msg = _('Please enter %s chars at maximum.');

        return sprintf($msg, $this->getMinlength());
    }

    /**
     * Get length of passed string.
     * Takes multibyte characters into account, if functions available.
     *
     * @param string $string
     *
     * @return int $length
     */
    public static function getStringLength($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, 'UTF-8');
        }

        return strlen(mb_convert_encoding($string, 'ISO-8859-1'));
    }

    protected function processValidationLogic($value)
    {
        if (self::getStringLength($value) < $this->getMinlength()) {
            return false;
        }

        return true;
    }
}
