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
 * Validates the lenght of a string with maxlength given.
 */
class MaxLength extends Validator
{
    public $maxlength;

    public function getMaxlength()
    {
        return $this->maxlength;
    }

    /**
     * Setter for the maximum length of the string.
     *
     * @param int $maxlength
     */
    public function setMaxlength($maxlength)
    {
        $this->maxlength = (int) $maxlength;
    }

    public function getErrorMessage()
    {
        $msg = _('The value exceeds the maxlength of %s chars');

        return sprintf($msg, $this->getMaxlength());
    }

    public function getValidationHint()
    {
        $msg = _('Please enter %s chars at maximum.');

        return sprintf($msg, $this->getMaxlength());
    }

    /**
     * Get length of passed string.
     * Takes multibyte characters into account, if functions available.
     *
     * @param  string  $string
     * @return integer $length
     */
    public static function getStringLength($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, 'utf8');
        }

        return strlen(utf8_decode($string));
    }

    protected function processValidationLogic($value)
    {
        if (self::getStringLength($value) > $this->getMaxlength()) {
            return false;
        }

        return true;
    }
}
