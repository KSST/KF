<?php

/**
 * Koch Framework
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 *
 * This file is part of https://github.com/KSST/KF
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * */

/**
 * Converts a UTF8-string into HTML entities.
 *
 * When using UTF-8 as a charset you want to convert multi-byte characters.
 * This function takes multi-byte characters up to level 4 into account.
 * Htmlentities will only convert 1-byte and 2-byte characters.
 * Use this function if you want to convert 3-byte and 4-byte characters also.
 *
 * @author silverbeat gmx  at
 *
 * @link http://www.php.net/manual/de/function.htmlentities.php#96648
 *
 * @param $utf8 string The UTF8-string to convert
 * @param $encodeTags booloean TRUE will convert "<" to "&lt;", Default = false
 *
 * @return string the converted HTML-string
 */
function UTF8_to_HTML($utf8, $encodeTags = false)
{
    $result      = '';
    $utf8_strlen = strlen((string) $utf8);

    for ($i = 0; $i < $utf8_strlen; ++$i) {
        $char  = $utf8[$i];
        $ascii = ord($char);

        if ($ascii < 128) {
            // one-byte character
            $result .= ($encodeTags) ? htmlentities((string) $char) : $char;
        } elseif ($ascii < 192) {
            // non-utf8 character or not a start byte
            $result .= ''; // leave this. would else be an empty elseif statement.
        } elseif ($ascii < 224) {
            // two-byte character
            $result .= htmlentities(substr((string) $utf8, $i, 2), ENT_QUOTES, 'UTF-8');
            ++$i;
        } elseif ($ascii < 240) {
            // three-byte character
            $ascii1  = ord($utf8[$i + 1]);
            $ascii2  = ord($utf8[$i + 2]);
            $unicode = (15 & $ascii)  * 4096 +
                       (63 & $ascii1) * 64 +
                       (63 & $ascii2);
            $result .= '&#' . $unicode;
            $i += 2;
        } elseif ($ascii < 248) {
            // four-byte character
            $ascii1  = ord($utf8[$i + 1]);
            $ascii2  = ord($utf8[$i + 2]);
            $ascii3  = ord($utf8[$i + 3]);
            $unicode = (15 & $ascii)  * 262144 +
                       (63 & $ascii1) * 4096 +
                       (63 & $ascii2) * 64 +
                       (63 & $ascii3);
            $result .= '&#' . $unicode;
            $i += 3;
        }
    }

    return $result;
}
