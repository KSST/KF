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

use Koch\Datatype\Conversion;

/**
 * Config Handler for XML Format (via SimpleXML).
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
     * @return mixed array | bool false
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
