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

class Url extends Validator
{
    public function getValidationHint()
    {
        return _('Please enter a valid URL.');
    }

    public function getErrorMessage()
    {
        return _('The value is no valid URL.');
    }

    protected function processValidationLogic($value)
    {
        /**
         * Note: filter_var() does not support IDNA.
         * The INTL extension provides the method idn_to_ascii().
         * It converts a multibyte URL to a punycode ASCII string.
         */
        if (extension_loaded('intl') === true) {
            $value = idn_to_ascii($value);
        }

        if (true === (bool) filter_var($value, FILTER_VALIDATE_URL, $this->getOptions())) {
            return true;
        } else {
            return false;
        }
    }
}
