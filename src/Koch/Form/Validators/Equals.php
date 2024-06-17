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

class Equals extends Validator
{
    /**
     * The equals to field.
     *
     * @var string
     */
    public $equalsTo;

    /**
     * EqualsTo Setter.
     *
     * @param string $equalsTo The equals to field
     */
    public function setEqualsTo($equalsTo)
    {
        $this->equalsTo = $equalsTo;
    }

    /**
     * EqualsTo Getter.
     *
     * @return string the equals to field
     */
    public function getEqualsTo()
    {
        return $this->equalsTo;
    }

    public function getValidationHint()
    {
        return _('The values must be equal.');
    }

    public function getErrorMessage()
    {
        return _('The values must be equal.');
    }

    protected function processValidationLogic($value)
    {
        if ($this->equalsTo === $value) {
            return true;
        } else {
            return false;
        }
    }
}
