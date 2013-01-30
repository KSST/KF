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
 * Config Handler for Json Format.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
class JSON
{
    /**
     * Read the config array from JSON file
     *
     * @param   string  The filename
     * @return mixed array | bool false
     */
    public static function readConfig($filename)
    {
        if (is_file($filename) === false or is_readable($filename) === false) {
            throw new \Koch\Exception\Exception(_('JSON Config File not existing or not readable.'));
        }

        // read file to get the JSON content
        $json_content = file_get_contents($filename);

        // transform JSON to PHP Array
        $json = json_decode($json_content, true);

        // fetch any error and handle the error, if any
        $json_error_type = json_last_error();
        if ($json_error_type !== JSON_ERROR_NONE) {
            throw new \Koch\Config\Exception\JsonException($filename, $json_error_type);
        }

        // return JSON as PHP array
        return $json;
    }

    /**
     * Write the config array to json file
     *
     * @param   string  The filename
     * @param   array   The configuration array
     * @return mixed|int|bool Number of bytes written to file, or false on failure.
     */
    public function writeConfig($file, array $array)
    {
        // transform array to json object notation
        $json_content = json_encode($array);

        // write json encoded content to file
        return (bool) file_put_contents($file, $json_content);
    }
}
