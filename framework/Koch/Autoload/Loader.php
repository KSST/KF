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

namespace<br>";

        if (is_string($filename) === true) {
            return self::includeFileAndMap($filename, $classname);
        } else {
            return false;
        }
    }

    /**
     * Include File (and register it to the autoloading map file)
     *
     * This procedure ensures, that the autoload mapping array dataset
     * is increased stepwise resulting in a decreasing number of autoloading tries.
     *
     * @param string $filename The file to be required
     *
     * @return bool True on success of require, false otherwise.
     */
    public static function includeFileAndMap($filename, $classname)
    {
        $filename = realpath($filename);

        // conditional include
        include_once $filename;

        // add class and filename to the mapping array
        self::addToMapping($filename, $classname);

        return true;
    }

    /**
     * Includes a file, if found.
     *
     * @param  string $filename  The file to be included
     * @param  string $classname (Optional) The classname expected inside this file.
     * @return bool   True on success of include, false otherwise.
     */
    public static function includeFile($filename, $classname = null)
    {
        $filename = realpath($filename);

        if (is_file($filename) === true) {
            include $filename;

            if (null === $classname) { // just a file include, classname unimportant

                return true;
            } elseif (class_exists($classname, false) === true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Writes the autoload mapping array into a file.
     * The target file is ROOT.'configuration/'.self::$autoloader
     * The content to be written is an associative array $array,
     * consisting of the old mapping array appended by a new mapping.
     *
     * @param $array associative array with relation of a classname to a filename
     */
    public static function writeAutoloadingMapFile($array)
    {
        if (is_writable(self::$mapfile) === false) {
            self::readAutoloadingMapFile();
        }

        if (is_writable(self::$mapfile) === true) {
            $bytes_written = file_put_contents(self::$mapfile, serialize($array), LOCK_EX);

            if ($bytes_written === false) {
                trigger_error('Autoloader could not write the map cache file: ' . self::$mapfile, E_USER_ERROR);
            } else {
                return true;
            }
        } else {
            trigger_error('Autoload cache file not writable: ' . self::$mapfile, E_USER_ERROR);
        }
    }

    /**
     * Reads the content of the autoloading map file and returns it unserialized.
     *
     * @return unserialized file content of autoload.config file
     */
    public static function readAutoloadingMapFile()
    {
        if (self::$mapfile === '') {
            throw new RuntimeException('No classmap file set. Use method ->setClassMapFile() to set one.');
        }

        // create file, if not existant
        if (is_file(self::$mapfile) === false) {
            $file_resource = fopen(self::$mapfile, 'a', false);
            fclose($file_resource);
            unset($file_resource);

            return array();
        } else { // load map from file
            // Note: delete the $mapfile file, if you get an unserialization error like "error at offset xy"
            return unserialize(file_get_contents(self::$mapfile));
        }
    }

    /**
     * Reads the autoload mapping array from APC.
     *
     * @return array automatically generated classmap
     */
    public static function readAutoloadingMapApc()
    {
        return apc_fetch('CLANSUITE_CLASSMAP');
    }

    /**
     * Writes the autoload mapping array to APC.
     *
     * @return array   automatically generated classmap
     * @return boolean True if stored.
     */
    public static function writeAutoloadingMapApc($array)
    {
        return apc_store('CLANSUITE_CLASSMAP', $array);
    }

    /**
     * Adds a new $classname to $filename mapping to the map array.
     * The new map array is written to apc or file.
     *
     * @param $filename  Filename is the file to load.
     * @param $classname Classname is the lookup key for $filename.
     * @return boolean True if added to map.
     */
    public static function addToMapping($filename, $classname)
    {
        self::$autoloaderMap = array_merge((array) self::$autoloaderMap, array($classname => $filename));

        if (defined('APC') and APC == true) {
            return self::writeAutoloadingMapApc(self::$autoloaderMap);
        } else {
            return self::writeAutoloadingMapFile(self::$autoloaderMap);
        }
    }

    /**
     * Getter for the autoloader classmap.
     *
     * @return array autoloader classmap.
     */
    public static function getAutoloaderClassMap()
    {
        return self::$autoloaderMap;
    }

    /**
     * Setter for the classmap file
     *
     * @param string classmap filepath.
     */
    public static function setClassMapFile($mapfile)
    {
        self::$mapfile = $mapfile;
    }

    /**
     * Setter for the inclusions classmap.
     *
     * @param array inclusions classmap (classname => file)
     */
    public static function setInclusionsClassMap(array $classmap)
    {
        self::$inclusionsClassmap = $classmap;
    }
}
