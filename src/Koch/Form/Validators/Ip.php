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
 * Validator for an IP address.
 * The validator accepts IPv4 and IPv6 addresses.
 * If you want only one version, use the flags FILTER_FLAG_IPV4
 * or FILTER_FLAG_IPV6 via setOptions().
 *
 * @see http://www.php.net/manual/en/filter.filters.validate.php
 */
class Ip extends Validator
{
    public function getErrorMessage()
    {
        return _('The value must be a IP.');
    }

    public function getValidationHint()
    {
        return _('Please enter a valid IP address.');
    }

    protected function processValidationLogic($value)
    {
        if (true === (bool) filter_var($value, FILTER_VALIDATE_IP, $this->getOptions())) {
            return true;
        } else {
            return false;
        }
    }
}
