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
 * Config Handler for YAML Format.
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
     * Write the config array to a yaml file.
     *
     * @param   string The yaml file.
     *
     * @return bool True, if successfully written, else False.
     */
    public static function write($file, array $array)
    {
        // prefer yaml, then syck, else use Spyc - faster one first
        if (extension_loaded('yaml')) {
            return yaml_emit_file($file, $array);
        } elseif (extension_loaded('syck')) {
            $yaml = syck_dump($array);
        } elseif (class_exists('Spyc')) {
            $spyc = new Spyc();
            $yaml = $spyc->dump($array);
        } else {
            throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
        }

        return (bool) file_put_contents($file, $yaml, LOCK_EX);
    }

    /**
     * Read the complete config file *.yaml.
     *
     * @param  string  The yaml file.
     *
     * @return array PHP array of the yaml file.
     */
    public static function read($file)
    {
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \Koch\Exception\Exception('YAML File ' . $file . ' not existing or not readable.');
        }

        if (extension_loaded('yaml')) {
            return yaml_parse_file($file);
        } elseif (extension_loaded('syck')) {
            $yaml = file_get_contents($file);

            return syck_load($yaml);
        } elseif (class_exists('Spyc')) {
            $spyc = new Spyc();
            $yaml = file_get_contents($file);

            return $spyc->load($yaml);
        }

        throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
    }
}
