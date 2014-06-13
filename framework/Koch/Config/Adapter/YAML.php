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
 * Koch Framework - Config Handler for YAML Format.
 *
 * Purpose: A configuration handler supporting the YAML file format.
 *
 * What is YAML?
 * 1) YAML Ain't Markup Language
 * 2) YAML(tm) (rhymes with "camel") is a straightforward machine parsable data serialization format
 * designed for human readability and interaction with scripting languages. YAML is optimized for
 * data serialization, configuration settings, log files, Internet messaging and filtering.
 *
 * The YAML support of this class is based around two parser libraries:
 * a) the php extension SYCK, which is written in C and available via PECL
 * b) the SPYC Library, which is pure PHP
 * This class prefers SYCK ofer SPYC for performance reasons.
 *
 * @link http://www.yaml.org/ YAML Website
 * @link http://www.yaml.org/spec/ YAML Format Specification
 * @link http://pecl.php.net/package/syck/ PECL SYCK Package maintained by Alexey Zakhlestin
 * @link http://github.com/why/syck/tree/master PECL SYCK Repository
 * @link http://spyc.sourceforge.net/ SPYC Library Website at Sourceforge
 */
class YAML implements AdapterInterface
{
    /**
     * Write the config array to a yaml file
     *
     * @param   string The yaml file.
     * @return boolean True, if successfully written, else False.
     */
    public static function write($file, array $array)
    {
        // prefer yaml, then syck, else use Spyc - faster one first
        if (extension_loaded('yaml') === true) {
            return yaml_emit_file($file, $array);
        } elseif (extension_loaded('syck') === true) {
            $yaml = syck_dump($array);
        } elseif (class_exists('Spyc') === true) {
            $spyc = new Spyc();
            $yaml = $spyc->dump($array);
        } else {
            throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
        }

        return (bool) file_put_contents($file, $yaml, LOCK_EX);
    }

    /**
     * Read the complete config file *.yaml
     *
     * @param  string  The yaml file.
     * @return array PHP array of the yaml file.
     */
    public static function read($file)
    {
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \Koch\Exception\Exception('YAML File ' . $file . ' not existing or not readable.');
        }

        if (extension_loaded('yaml') === true) {
            return yaml_parse_file($file);
        } elseif (extension_loaded('syck') === true) {
            $yaml = file_get_contents($file);

            return syck_load($yaml);
        } elseif (class_exists('Spyc') === true) {
            $spyc  = new Spyc();
            $yaml = file_get_contents($file);

            return $spyc->load($yaml);
        }

        throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
    }
}
