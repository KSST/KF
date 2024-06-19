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

        mb_internal_encoding('UTF-8');
    }
}
