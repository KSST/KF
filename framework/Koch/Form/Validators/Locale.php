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
        require __DIR__ . '/../../Localization/Locales.php';

        if (strlen($locale) === 2) {
            $short_code = $locale;
        } else {
            // 1) transform "de-DE" into "de_DE" (underscore is a valid sublocale separator)
            $locale = str_replace('-', '_', $locale);

            // 2) turn lowercase sublocale into uppercase sublocale, split first
            $locale = explode('_', $locale);

            // key 0 returns "de" from "de_DE" as the locale short_code
            $short_code = $locale[0];

            // finally the lowercase fix, turns "de_de" into "de_DE" ()
            $locale[1] = strtoupper($locale[1]);
            $locale = implode('_', $locale);
        }

        if (array_key_exists($short_code, $l10n_langs) === true) {
            // return if locale is just short_code, e.g. "de"
            if (strlen($locale) == 2) {
                return true;
            }
             // fetch sublocales (looks in "de" array, returns "de_AT", "de_CH", "de_DE"...)
            $sublocales = $l10n_langs[$short_code];
        } else {
            // there are no sublocales for this locale short code
            return false;
        }

        // is valid sublocale, e.g. de has de_DE?
        if (array_key_exists($locale, array_flip($sublocales))) {
            return true;
        } else {
            return false;
        }
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
