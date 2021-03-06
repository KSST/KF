<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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

namespace Koch\Datatype;

/**
 * Koch Framework - Class for advanced Datatype Conversions.
 */
class Conversion
{
    public static function xmlToArray($xml, $recursionDepth = 0)
    {
        XML::toArray($xml, $recursionDepth);
    }

    /**
     * Converts a PHP array to XML (via XMLWriter).
     *
     * @param $array PHP Array
     *
     * @return string XML string.
     */
    public static function arrayToXml($array)
    {
        // initialize new XML Writer in memory
        $xml = new \XmlWriter();
        $xml->openMemory();

        // with <root> element as top level node
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('root');

        // add the $array data in between
        self::writeArray($xml, $array);

        // close with </root>
        $xml->endElement();

        // dump memory
        return $xml->outputMemory(true);
    }

    /**
     * writeArray() is a recursive looping over an php array,
     * adding all it's elemets to an XMLWriter object.
     * This method is used by arrayToXML().
     *
     * @see arrayToXML()
     *
     * @param $xml XMLWriter Object
     * @param $array PHP Array
     */
    public static function writeArray(\XMLWriter $xml, array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $xml->startElement($key);

                // recursive call
                self::writeArray($xml, $value);

                $xml->endElement();

                continue;
            }

            $xml->writeElement($key, $value);
        }
    }

    /**
     * Converts a SimpleXML String recursivly to an Array.
     *
     * @author Jason Sheets <jsheets at shadonet dot com>
     *
     * @return array
     */
    public static function simpleXMLToArrayLight($simplexml)
    {
        $array = [];

        if ($simplexml) {
            foreach ($simplexml as $k => $v) {
                if ($simplexml['list'] === true) {
                    $array[] = self::SimpleXMLToArrayLight($v);
                } else {
                    $array[$k] = self::SimpleXMLToArrayLight($v);
                }
            }
        }

        if (count($array) > 0) {
            return $array;
        } else {
            // WARNING! Type Conversion drops childs and attributes.
            return (string) $simplexml;
        }
    }

    /**
     * Converts an Object to an Array.
     *
     * @param $object object to convert
     *
     * @return array
     */
    public static function objectToArray($object)
    {
        $array = null;
        if (is_object($object)) {
            $array = [];
            foreach (get_object_vars($object) as $key => $value) {
                if (is_object($value)) {
                    $array[$key] = self::objectToArray($value);
                } else {
                    $array[$key] = $value;
                }
            }
        }

        return $array;
    }

    /**
     * Converts an Array to an Object.
     *
     * @param $array array to convert to an object
     *
     * @return array
     */
    public function arrayToObject($array)
    {
        if (is_array($array) === false) {
            return $array;
        }

        $object = new stdClass();

        if (is_array($array) and count($array) > 0) {
            foreach ($array as $name => $value) {
                $name = mb_strtolower(trim($name));

                if (empty($name) === false) {
                    // WATCH OUT ! Recursion.
                    $object->$name = self::arrayToObject($value);
                }
            }

            return $object;
        } else {
            return false;
        }
    }
}
