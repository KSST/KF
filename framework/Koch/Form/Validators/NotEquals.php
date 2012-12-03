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

class Equals extends Validator
{
    /**
     * The not equals to field
     *
     * @var string
     */
    public $notEqualsTo;

    /**
     * EqualsTo Setter
     *
     * @param string $equalsTo The equals to field
     */
    public function setNotEqualsTo($notEqualsTo)
    {
        $this->notEqualsTo = $notEqualsTo;
    }

    /**
     * EqualsTo Getter
     *
     * @return string the equals to field
     */
    public function getNotEqualsTo()
    {
        return $this->notEqualsTo;
    }

    public function getValidationHint()
    {
        return _('The values must not be equal.');
    }

    public function getErrorMessage()
    {
        return _('The values must not be equal.');
    }

    protected function processValidationLogic($value)
    {
        if ($this->notEqualsTo != $value) {
            return true;
        } else {
            return false;
        }
    }
}
