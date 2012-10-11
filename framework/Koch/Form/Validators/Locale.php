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
 * Validates the given language is really a language.
 */
class Locale extends Validator
{
    public function getValidationHint()
    {
        return _('Please select a valid locale.');
    }

    public function getErrorMessage()
    {
        return _('The value is not a valid locale.');
    }

    public static function isLocale($locale)
    {
        // fetch data table(s) with "Locales"
        require dirname(dirname(__DIR__)) . '/Localization/Locales.php';

        // turns "de_DE" into "de"
        $short_code = mb_substr($locale, 0, 2);

        if (($l10n_langs[$short_code] !== null) or (array_key_exists($short_code, $l10n_langs) === true)) {
            // looks in "de" array, returns "de_AT", "de_CH", "de_DE"...
            $sublocales = $l10n_langs[$short_code];
        } else {
            // there are no sublocales for this locale short code
            return false;
        }

        if (true === in_array($locale, array_flip($sublocales))) {
            return true;
        } else {
            return false;
        }

        unset($l10n_langs);
    }

    protected function processValidationLogic($value)
    {
        if (true === self::isLocale($value)) {
            return true;
        } else {
            return false;
        }
    }
}
