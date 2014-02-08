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
 */

namespace Koch\Localization;

/**
 * Koch Framework - Class for Initalizing UTF8.
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

        // do not accept mbstring function overloading set in php.ini
        if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
            throw new \Koch\Exception\Exception(
                'The string functions are overloaded by mbstring. Please stop that. ' .
                'Check the "php.ini" setting: "mbstring.func_overload".'
            );
        }

        mb_internal_encoding('UTF-8');
    }
}
