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

namespace Koch\Config\Adapter;

/**
 * Koch Framework - Config Handler for Json Format.
 */
class JSON implements AdapterInterface
{
    /**
     * Read the config array from JSON file
     *
     * @param   string  The filename
     * @return mixed array | boolean false
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
    public static function writeConfig($file, array $array)
    {
        // transform array to json object notation
        $json_content = json_encode($array);

        // write json encoded content to file
        return (bool) file_put_contents($file, $json_content);
    }
}
