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

namespace Koch\Config\Adapter;

/**
 * Config Handler for INI Format.
 */
class INI implements AdapterInterface
{
    /**
     * Writes a .ini Configfile
     * This method writes the configuration values specified to the filename.
     *
     * @param string $file  Filename of .ini to write
     * @param array  $array Associative Array with Ini-Values
     *
     * @return mixed/boolean Returns the amount of bytes written to the file, or FALSE on failure.
     */
    public static function write($file, array $array)
    {
        if (empty($file)) {
            throw new \Koch\Exception\Exception('Parameter $file is not given.');
        }

        // when ini file exists, get old config array
        if (is_file($file)) {
            $oldArray = self::read($file);

            // array merge: overwrite the array to the left, with the array to the right, when keys identical
            $array = array_replace_recursive($oldArray, $array);
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
            if (is_array($item)) {
                // write a comment header block
                $content .= "\n";
                $content .= ';----------------------------------------' . "\n";
                $content .= '; ' . $key . "\n";
                $content .= ';----------------------------------------' . "\n";

                // write a parseable [array_header] block
                $content .= '[' . $key . ']' . "\n";

                // for every element after that
                foreach ($item as $key2 => $item2) {
                    $content .= self::isNumericOrBoolean($key2, $item2);
                }
            } else {
                // it's a value
                $content .= self::isNumericOrBoolean($key, $item);
            }
        }

        // add php closing tag
        $content .=  "\n ; DO NOT REMOVE THIS LINE */ ?>";

        // write to file
        return (bool) file_put_contents($file, $content);
    }

    /**
     * String formatting based on type.
     *
     *
     * @return string The result string. No you know more.
     */
    public static function isNumericOrBoolean(mixed $key, mixed $item)
    {
        $content = '';

        if (is_numeric($item) || is_bool($item)) {
            // write numeric and boolean values without quotes
            $content .= $key . ' = ' . $item . "\n";
        } else {
            // it's a string - write value with quotes
            $content .= $key . ' = "' . $item . '"' . "\n";
        }

        return $content;
    }

    /**
     * Read the complete config file *.ini.php.
     *
     * @param   string  The filename
     *
     * @return array | boolean false
     */
    public static function read($file)
    {
        // check ini_filename exists
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \InvalidArgumentException('File not found: ' . $file);
        }

        return parse_ini_file($file, true);
    }
}
