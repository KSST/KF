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

namespace<br>";

        if (is_string($filename)) {
            return self::includeFileAndMap($filename, $classname);
        }

        return false;
    }

    /**
     * Include File (and register it to the autoloading map file).
     *
     * This procedure ensures, that the autoload mapping array dataset
     * is increased stepwise resulting in a decreasing number of autoloading tries.
     *
     * @param string $filename  The file to be required
     * @param string $classname
     *
     * @return bool True on success of require, false otherwise.
     */
    public static function includeFileAndMap($filename, $classname)
    {
        $filename = realpath($filename);

        // conditional include
        include_once $filename;

        // add class and filename to the mapping array
        self::addMapping($classname, $filename);

        return true;
    }

    /**
     * Includes a file, if found.
     *
     * @param string $filename  The file to be included
     * @param string $classname (Optional) The classname expected inside this file.
     *
     * @return bool True on success of include, false otherwise.
     */
    public static function includeFile($filename, $classname = null)
    {
        $filename = realpath($filename);

        if (is_file($filename)) {
            include $filename;

            if (null === $classname || class_exists($classname, false)) {
                return true;
            }
        }

        return false;
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
            // create map file first
            self::readAutoloadingMapFile();
        }

        if (is_writable(self::$mapfile)) {
            return (bool) file_put_contents(self::$mapfile, serialize($array), LOCK_EX);
        } else {
            throw new \RuntimeException('Autoload cache file not writable: ' . self::$mapfile);
        }
    }

    /**
     * Reads the content of the autoloading map file and returns it unserialized.
     *
     * @return array<string> file content of autoload.config file
     */
    public static function readAutoloadingMapFile()
    {
        // create file, if not existant
        if (is_file(self::$mapfile)) {
            $file_resource = fopen(self::$mapfile, 'a', false);
            fclose($file_resource);
            unset($file_resource);

            return [];
        } else { // load map from file
            try {
                return (array) unserialize(file_get_contents(self::$mapfile));
            } catch (\Exception $e) {
                // delete mapfile, on unserialization error (error at offset xy)
                unlink(self::$mapfile);
            }
        }
    }

    /**
     * Reads the autoload mapping array from APC.
     *
     * @return array automatically generated classmap
     */
    public static function readAutoloadingMapApc()
    {
        return apc_fetch('KF_CLASSMAP');
    }

    /**
     * Writes the autoload mapping array to APC.
     *
     * @return bool automatically generated classmap
     * @return bool True if stored.
     */
    public static function writeAutoloadingMapApc($array)
    {
        return apc_store('KF_CLASSMAP', $array);
    }

    /**
     * Adds a new $classname to $filename mapping to the map array.
     * The new map array is written to apc or file.
     *
     * @param string $class Classname is the lookup key for $filename.
     * @param string $file  Filename is the file to load.
     *
     * @return bool True if added to map.
     */
    public static function addMapping($class, $file)
    {
        self::$autoloaderMap = array_merge((array) self::$autoloaderMap, [$class => $file]);

        if (defined('APC') and APC === true) {
            return self::writeAutoloadingMapApc(self::$autoloaderMap);
        }

        return self::writeAutoloadingMapFile(self::$autoloaderMap);
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
     * Setter for the classmap file.
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

    /**
     * Registers the autoloader.
     */
    public static function register($mapfile)
    {
        self::setClassMapFile($mapfile);

        spl_autoload_register([__CLASS__, 'autoload'], true, true);
    }

    /**
     * Unregisters the autoloader.
     */
    public static function unregister()
    {
        spl_autoload_unregister([__CLASS__, 'autoload']);
    }
}
