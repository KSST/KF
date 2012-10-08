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
     * @param  string $filename The file to be required
     * @return bool   True on success of require, false otherwise.
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
     * Require File if file found
     *
     * @param  string $filename  The file to be required
     * @param  string $classname The classname (hopefully) inside this file.
     * @return bool
     */
    public static function requireFile($filename, $classname = null)
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
        self::$autoloader_map = array_merge((array) self::$autoloader_map, array( $classname => $filename ));

        if (defined('APC') and APC  == true) {
            return self::writeAutoloadingMapApc(self::$autoloader_map);
        } else {
            return self::writeAutoloadingMapFile(self::$autoloader_map);
        }
    }

    /**
     * Includes a certain library classname by using a manually maintained autoloading map.
     * Functionally the same as self::autoloadInclusions().
     *
     * You can load directly:
     * Snoopy, SimplePie, PclZip, graph, GeSHi, feedcreator, browscap, bbcode
     *
     * You can also pass a custom map, like so:
     * loadLibrary('xtemplate', ROOT_LIBRARIES . 'xtemplate/xtemplate.class.php' )
     *
     * @param  string $classname Library classname to load.
     * @param  string $path      Path to the class.
     * @return true   if classname was included
     */
    public static function loadLibrary($classname, $path = null)
    {
        // check if class was already loaded
        if (true === class_exists($classname, false)) {
            return true;
        }

        $classname = strtolower($classname);

        if ($path !== null) {
            $map = array($classname, $path);
        } else {
            // autoloading map - ROOT_LIBRARIES/..
            $map = array(
                'snoopy'        => ROOT_LIBRARIES . 'snoopy/Snoopy.class.php',
                'simplepie'     => ROOT_LIBRARIES . 'simplepie/simplepie.inc',
                'pclzip'        => ROOT_LIBRARIES . 'pclzip/pclzip.lib.php',
                'graph'         => ROOT_LIBRARIES . 'graph/graph.class.php',
                'geshi'         => ROOT_LIBRARIES . 'geshi/geshi.php',
                'feedcreator'   => ROOT_LIBRARIES . 'feedcreator/feedcreator.class.php',
                'browscap'      => ROOT_LIBRARIES . 'browscap/Browscap.php',
                'bbcode'        => ROOT_LIBRARIES . 'bbcode/stringparser_bbcode.class.php',
            );
        }

        // check if classname is in autoloading map
        if ($map[$classname] !== null) {
            // get filename for that classname
            $filename = $map[$classname];

            // and include that one
            if (true === self::requireFile($filename, $classname)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Getter for the autoloader classmap.
     *
     * @return array autoloader classmap.
     */
    public static function getAutoloaderClassMap()
    {
        return self::$autoloader_map;
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
        self::$inclusions_classmap = $classmap;
    }
}
