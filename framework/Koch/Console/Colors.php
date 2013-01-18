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

namespace Koch\Console;

/**
 * Command Line Colors
 *
 * The PHP Command Line Interface (CLI) has not built-in coloring for script output.
 * This class adds some color to PHP CLI output by using ANSI escape codes/sequences.
 *
 * These escape codes work on Linux BASH shells.
 * For ANSI coloring on the Windows console, you might consider using ANSICON.
 * @link https://github.com/adoxa/ansicon
 *
 * Ansi Escape Sequences takes from
 * @link http://ascii-table.com/ansi-escape-sequences.php
 * @link https://wiki.archlinux.org/index.php/Color_Bash_Prompt
 *
 * @category    Koch
 * @package     Core
 * @subpackage  CLI
 */
class Colors
{
    // Ansi foreground colors
    private static $foreground = array(
        'black' => '0;30',
        'dark_gray' => '1;30',
        'red' => '0;31',
        'bold_red' => '1;31',
        'green' => '0;32',
        'bold_green' => '1;32',
        'brown' => '0;33',
        'yellow' => '1;33',
        'blue' => '0;34',
        'bold_blue' => '1;34',
        'purple' => '0;35',
        'bold_purple' => '1;35',
        'cyan' => '0;36',
        'bold_cyan' => '1;36',
        'white' => '1;37',
        'bold_gray' => '0;37',
    );

    // Ansi background colors
    private static $background = array(
        'black' => '40',
        'red' => '41',
        'magenta' => '45',
        'yellow' => '43',
        'green' => '42',
        'blue' => '44',
        'cyan' => '46',
        'light_gray' => '47',
    );

    // Ansi Modifiers
    private static $modifier = array(
        'reset'         => '0',
        'bold'          => '1',
        'italic'        => '3',
        'underline'     => '4',
        'blink'         => '5',
        'blinkfast'     => '6',
        'inverse'       => '7',
        'strikethrough' => '9'
    );

    // Unicode Symbol Name to Octal Escape Sequence
    private static $unicode = array(
        'ok' => "✓",        // "check mark" - \u221A
        'fail' => "✖",      // "ballot x" - \u00D7
        'big fail' => "✖",
        'big ok' => "✔"
    );

    public static function unicodeSymbol($symbol, $options = null)
    {
        if (isset(static::$unicode[$symbol])) {
            $symbol = static::$unicode[$symbol];
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Invalid unicode symbol specified: "%s". Expected one of (%s).',
                $symbol,
                implode(', ', array_keys(static::$unicode))
            ));
        }

        //return $symbol;
        return self::write($symbol, $options);
    }

    public static function write($text, $foreground = null, $background = null, $modifiers = null)
    {
        if (is_array($foreground)) {
             $options = self::setOptions($foreground);

             $foreground = array_shift($options); // 0
             $background = array_shift($options); // 1
             $modifiers = $options;
        }

        $codes = array();

        if (null !== $foreground and isset(self::$foreground[$foreground])) {
            $codes[] = static::$foreground[$foreground];
        }

        if (null !== $background and isset(self::$background[$background])) {
            $codes[] = static::$background[$background];
        }

        if (null !== $modifiers) {
            // handle comma separated list of modifiers
            if (is_string($modifiers)) {
                $modifiers = explode(',', $modifiers);
            }
            foreach ($modifiers as $modifier) {
                 if (isset(static::$modifier[$modifier])) {
                    $codes[] = static::$modifier[$modifier];
                }
            }
        }

        $escapeCodes = implode(';', $codes);

        return sprintf("\033[%sm%s\033[0m", $escapeCodes, $text);
    }

    public static function setOptions($options)
    {
        // string to array
        if (is_string($options) === true) {
            $options = explode(',', $options);
        }

        return $options;
    }

    /**
     * Colorizes a specific parts of a text, which are matched by search_regexp.
     *
     * @param string
     * @param string regexp
     * @param mixed|string|array
     */
    public static function colorize($text, $search_regexp, $color)
    {
        $callback = function ($matches) use ($color) {
            return Colors::write($matches[1], $color);
        };

        $ansi_text = preg_replace_callback("/($search_regexp)/", $callback, $text);

        return is_null($ansi_text) ? $text : $ansi_text;
    }

    public static function colorizeReturnValue($value)
    {
        return ($value == 0) ? self::unicodeSymbol('x', 'red') : self::unicodeSymbol('ok', 'green');
    }
}
