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

use Koch\Datatype\Conversion;

/**
 * Koch Framework - Config Handler for XML Format (via SimpleXML).
 */
class XML implements AdapterInterface
{
    /**
     * Write the configarray to the xml file
     *
     * @param string The filename
     * @param array  Array to transform and write as xml
     * @return boolean array | boolean false
     */
    public static function write($file, array $array)
    {
        $xml = Conversion::arrayToXML($array);

        return (bool) file_put_contents($file, $xml);
    }

    /**
     * Read the config array from xml file
     *
     * @param   string  The filename
     * @param string $file
     * @return mixed array | boolean false
     */
    public static function read($file)
    {
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \InvalidArgumentException('XML File ' . $file . ' not existing or not readable.');
        }

        $xml = simplexml_load_file($file);

        return \Koch\Datatype\XML::toArray($xml);
    }
}
