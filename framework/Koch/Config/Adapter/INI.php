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

namespace Koch\Config\Adapter;

/**
 * Koch Framework - Config Handler for INI Format.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
class INI
{
    /**
     * Writes a .ini Configfile
     * This method writes the configuration values specified to the filename.
     *
     * @param  string        $file  Filename of .ini to write
     * @param  array         $array Associative Array with Ini-Values
     * @return mixed/boolean Returns the amount of bytes written to the file, or FALSE on failure.
     */
    public static function writeConfig($file, array $array)
    {
        if (empty($file) === true) {
            throw new \Koch\Exception\Exception('Parameter $file is not given.');
        }

        // when ini file exists, get old config array
        if (is_file($file) === true) {
            $old_config_array = self::readConfig($file);

            // array merge: overwrite the array to the left, with the array to the right, when keys identical
            $config_array = array_replace_recursive($old_config_array, $array);
        } else {
            // the config array = the incoming assoc_array
            $config_array = $array;
        }

        // attach an security header at the top of the ini file
        $content = '';
        $content .= "; <?php die('Access forbidden.'); /* DO NOT MODIFY THIS LINE! ?>\n";
        $content .= ";\n";
        $content .= "; Koch Framework Configuration File :\n";
        $content .= '; ' . $file . "\n";
        $content .= ";\n";
        $content .= '; This file was generated on ' . date('d-m-Y H:i') . "\n";
        $content .= ";\n\n";

        // loop over every array element
        foreach ($config_array as $key => $item) {
            // check if it's an array, if so, it's a section heading
            if (is_array($item) === true) {
                // write a comment header block
                $content .= PHP_EOL;
                $content .= ';----------------------------------------' . PHP_EOL;
                $content .= '; ' . $key . PHP_EOL;
                $content .= ';----------------------------------------' . PHP_EOL;

                // write a parseable [array_header] block
                $content .= '[' . $key . ']' . PHP_EOL;

                // for every element after that
                foreach ($item as $key2 => $item2) {
                    if (is_numeric($item2) === true or is_bool($item2) === true) {
                        // write numeric and boolean values without quotes
                        $content .= $key2 . ' = "' . $item2 . '"' . PHP_EOL;
                    } else {
                        // write value with quotes
                        $content .= $key2 . ' = "' . $item2 . '"' . PHP_EOL;
                    }
                }
            } else {
                // it's a value
                if (is_numeric($item) === true or is_bool($item) === true) {
                    // write numeric and boolean values without quotes
                    $content .= $key . ' = "' . $item . '"' . PHP_EOL;
                } else {
                    // it's a string - write value with quotes
                    $content .= $key . ' = "' . $item . '"' . PHP_EOL;
                }
            }
        }

        // add php closing tag
        $content .= PHP_EOL . '; DO NOT REMOVE THIS LINE */ ?>';

        // write to file
        return (bool) file_put_contents($file, $content);
    }

    /**
     * Read the complete config file *.ini.php
     *
     * @param   string  The filename
     * @return array | boolean false
     */
    public static function readConfig($file)
    {
        // check ini_filename exists
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \InvalidArgumentException('File not found: ' . $file, 4);
        }

        return parse_ini_file($file, true);
    }
}
