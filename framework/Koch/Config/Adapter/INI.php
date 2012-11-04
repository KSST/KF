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

namespace Koch\Config\Adapter;

/**
 * Config Handler for INI Format.
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
            $array = array_replace_recursive($old_config_array, $array);
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
        foreach ($array as $key => $item) {
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
