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

namespace Koch\Localization;

/**
 * Class for Initalizing UTF8.
 *
 * The framework requires the PHP extension mbstring.
 */
class Utf8
{
    public static function initialize()
    {
        if (extension_loaded('mbstring') === false) {
            throw new \Koch\Exception\Exception('The PHP extension "mbstring" is required.');
        }

        // Check if PHP version is less than 8.0.0
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            // Do not accept mbstring function overloading set in php.ini
            if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
                throw new \Koch\Exception\Exception(
                    'The string functions are overloaded by mbstring. Please stop that. ' .
                    'Check the "php.ini" setting: "mbstring.func_overload".'
                );
            }
        }

        mb_internal_encoding('UTF-8');
    }
}
