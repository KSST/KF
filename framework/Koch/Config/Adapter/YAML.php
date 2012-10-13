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
 * Config Handler for YAML Format.
 *
 * Purpose: This Confighandler supports the YAML-Fileformat.
 *
 * What is YAML?
 * 1) YAML Ain't Markup Language
 * 2) YAML(tm) (rhymes with "camel") is a straightforward machine parsable data serialization format
 * designed for human readability and interaction with scripting languages. YAML is optimized for
 * data serialization, configuration settings, log files, Internet messaging and filtering.
 *
 * The YAML Support of this class is based around two parser libraries:
 * a) the php extension SYCK (available via PECL)
 * b) the SPYC Library.
 *
 * @link http://www.yaml.org/ YAML Website
 * @link http://www.yaml.org/spec/ YAML Format Specification
 * @link http://pecl.php.net/package/syck/ PECL SYCK Package maintained by Alexey Zakhlestin
 * @link http://github.com/why/syck/tree/master PECL SYCK Repository
 * @link http://spyc.sourceforge.net/ SPYC Library Website at Sourceforge
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Configuration
 */
class YAML
{
    /**
     * Constructor
     */
    public function __construct($file = null)
    {
        return self::readConfig($file);
    }

    /**
     * Write the config array to a yaml file
     *
     * @param   string  The filename
     * @return array | boolean false
     * @todo fix this return true/false thingy
     */
    public static function writeConfig($file, array $array)
    {
        $spyc_lib = __DIR__ . '/../../../vendor/spyc/Spyc.class.php';

        /**
         * transform PHP Array into YAML Format
         */

        // take syck, as the faster one first
        if ( extension_loaded('syck') ) {
            // convert to YAML via SYCK
            $yaml = syck_dump($data);
        }
        // else check, if we have spyc as library
        elseif (is_file($spyc_lib) === true) {
            // ok, load spyc
            if (false === class_exists('Spyc', false)) {
                include $spyc_lib;
            }

            $spyc = new Spyc();

            // convert to YAML via SPYC
            $yaml = $spyc->dump($array);
        } else { // we have no YAML Parser - too bad :(
            throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
        }

        /**
         * write array
         */

        // write YAML content to file
        file_put_contents($file, $yaml);
    }

    /**
     *  Read the complete config file *.yaml
     *
     * @param   string  The yaml filename
     * @return array
     */
    public static function readConfig($file)
    {
        // check if the filename exists
        if (is_file($file) === false or is_readable($file) === false) {
            throw new \Koch\Exception\Exception('YAML File ' . $file . ' not existing or not readable.');
        }

        // init
        $array = '';
        $yaml_content = '';

        // read the yaml content of the file
        $yaml_content = file_get_contents($file);

        /**
         * check if the php extension SYCK is available as parser
         * SYCK is written in C, so it's implementation is faster then SPYC, which is pure PHP.
         */
        if (extension_loaded('syck')) { // take the faster one first
            // syck_load accepts a YAML string as input and converts it into a PHP data structure
            $array = syck_load($yaml_content);
        }
        // else check if we habe spyc as a library
        elseif (is_file(ROOT_LIBRARIES . '/spyc/Spyc.class.php') === true) {
            // ok, load spyc
            if (false === class_exists('Spyc', false)) {
                include ROOT_LIBRARIES . '/spyc/Spyc.class.php';
            }

            // instantiate
            $spyc = new Spyc();

            // parse the yaml content with spyc
            $array = $spyc->load($yaml_content);
        } else { // we have no YAML Parser - too bad :(
            throw new \Koch\Exception\Exception('No YAML Parser available. Get Spyc or Syck!');
        }

        return $array;
    }
}
