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
        /**
         * Note: filter_var() does not support IDNA.
         * The INTL extension provides the method idn_to_ascii().
         * It converts a multibyte URL to a punycode ASCII string.
         */
        if (function_exists('idn_to_ascii')) {
            $value = idn_to_ascii($value);
        }

        if (true === (bool) filter_var($value, FILTER_VALIDATE_IP, $this->getOptions())) {
            return true;
        } else {
            return false;
        }
    }
}
