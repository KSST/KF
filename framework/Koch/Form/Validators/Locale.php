<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
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

        // @todo try to skip str_replace, explode, implode and use sscanf
        // list($short_code, $sublocale) = sscanf($locale, '%s-%s');

        if (strlen($locale) === 2) {
            $short_code = $locale;
        } else {
            // 1) transform "de-DE" into "de_DE"
            // Transform RFC 4646 language tags which use hyphen (minus) instead of underscore,
            // into the more traditional underscore-using identifiers.
            $locale = str_replace('-', '_', $locale);

            // 2) turn lowercase sublocale into uppercase sublocale, split first
            $locale = explode('_', $locale);

            // key 0 returns "de" from "de_DE" as the locale short_code
            $short_code = $locale[0];

            // finally the lowercase fix, turns "de_de" into "de_DE" ()
            $locale[1] = strtoupper($locale[1]);
            $locale = implode('_', $locale);
        }

        if ((isset($l10n_langs[$short_code]) === true) || (array_key_exists($short_code, $l10n_langs) === true)) {
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
        if ((isset($sublocales[$locale]) === true) || (array_key_exists($locale, array_flip($sublocales)))) {
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
