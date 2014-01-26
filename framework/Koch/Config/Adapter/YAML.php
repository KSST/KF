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
class YAML
{
    /**
     * Write the config array to a yaml file
     *
     * @param   string The yaml file.
     * @return boolean True, if successfully written, else False.
     */
    public static function writeConfig($file, array $array)
    {
        // prefer syck, else use Spyc - faster one first
        if (extension_loaded('syck') === true) {
            $yaml = syck_dump($array);
        } elseif (class_exists('Spyc') === true) {
            $spyc = new Spyc();
            $yaml = $spyc->dump($array);
        } else {
            throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
        }

        return (bool) file_put_contents($file, $yaml);
    }

    /**
     * Read the complete config file *.yaml
     *
     * @param  string  The yaml file.
     * @return array   PHP array of the yaml file.
     */
    public static function readConfig($file)
    {
        // check if the filename exists
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \Koch\Exception\Exception('YAML File ' . $file . ' not existing or not readable.');
        }

        $array = '';

        $yaml = file_get_contents($file);

        if (extension_loaded('syck') === true) {
            $array = syck_load($yaml);
        } elseif (class_exists('Spyc') === true) {
            $spyc  = new Spyc();
            $array = $spyc->load($yaml);
        } else {
            throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
        }

        return $array;
    }

}