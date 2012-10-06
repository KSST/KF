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
        $msg = _('The value deceeds (is less than) the Minlength of %s chars.');

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
     * @param  string  $string
     * @return integer $length
     */
    public static function getStringLength($string)
    {
        if (function_exists('iconv_strlen')) {
            return iconv_strlen($string, 'UTF-8');
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($string, 'utf8');
        }

        return strlen(utf8_decode($string));
    }

    protected function processValidationLogic($value)
    {
        if (self::getStringLength($value) < $this->getMinlength()) {
            return false;
        }

        return true;
    }
}
