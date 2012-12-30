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
        'check' => '\342\234\223', // check mark, like on check lists.
        'x' => '\342\234\227'     // x, like when voting, called "ballot x".
    );

    private static $reset = "\033[0m";

    /**
     * Returns true if ANSI colorization is supported.
     *
     * @return boolean true if colorization is supported, false otherwise.
     */
    protected static function hasColorSupport()
    {
        // @codeCoverageIgnoreStart

        // on windows, you need ANSICON or ConEmu
        if (DIRECTORY_SEPARATOR == '\\') {
            return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        }

        // no tty console
        return function_exists('posix_isatty') && @posix_isatty(STDOUT);

        // @codeCoverageIgnoreEnd
    }

    public static function unicodeSymbol($symbol)
    {
        if (isset(self::$unicode[$symbol])) {
            return self::$unicode[$symbol];
        }
    }

    public static function write($string, $foreground = null, $background = null, $modifier = null)
    {
        /*if (self::hasColorSupport() === false) {
            return $string;
        }*/

        if (is_array($foreground)) {
             $options = self::options($foreground);

             $foreground = $options['fg'];
             $background = $options['bg'];
             $modifier = $options['m'];
        }

        $escapePrefix = '';

        if (null !== $foreground and isset(self::$foreground[$foreground])) {
            $escapePrefix .= "\033[" . self::$foreground[$foreground] . "m";
        }

        if (null !== $background and isset(self::$background[$background])) {
            $escapePrefix .= "\033[" . self::$background[$background] . "m";
        }

        if (null !== $modifier and isset(self::$modifier[$modifier])) {
            $escapePrefix .= "\033[" . self::$modifier[$modifier] . "m";
        }

        // Add string and end coloring
        return $escapePrefix . $string . self::$reset;
    }

    public static function options($in)
    {
        $codes = array();

        // string to array
        $in = is_string($in) ? explode(',', $in) : $in;

        // numeric indexed array to named array
        if (is_array($in) and !isset($in['fg'])) {
            if (isset($in[0])) {
                $codes['fg'] = $in[0];
            }
            if (isset($in[1])) {
                $codes['bg'] = $in[1];
            }
            if (isset($in[2])) {
                $codes['m'] = $in[2];
            }
        }

        return $codes;
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
        $ansi_text = preg_replace_callback(
            "/($search_regexp)/",
            function ($matches) use ($color) {
                return self::write($matches[1], $color);
            },
            $text
        );

        return is_null($ansi_text) ? $text : $ansi_text;
    }

    public function returnValue($value)
    {
        return ($value == 0) ? self::unicodeSymbol('x') : self::unicodeSymbol('check');
    }
}
