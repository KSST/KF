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

namespace Koch\Autoload;

/**
 * Koch Framework - Class for Autoloading of Files by Classname.
 *
 * This Loader overwrites the Zend Engines _autoload() with our own user defined loading functions.
 * The main function of this class is autoload().
 * It's registered via spl_autoload_register($load_function).
 * Autoload will run, if a file is not found.
 * There are several loader-functions, which are called in a chain by autoload().
 * The procedure is (1) exclusions, (2) inclusions, (3) mapping (file or apc), (4) include path (psr-0).
 *
 * Usage:
 * 1) include this file
 * 2) spl_autoload_register('Koch\Autoload\Loader::autoload');
 *
 * PHP Manual: __autoload
 *
 * @link http://www.php.net/manual/en/language.oop5.autoload.php
 *
 * PHP Manual: spl_autoload_register
 * @link http://www.php.net/manual/de/function.spl-autoload-register.php
 */
class Loader
{
    /**
     * Generated Classmap from File or APC.
     *
     * @var array
     */
    private static $autoloaderMap = [];

    /**
     * A manually defined classmap you might set from outside.
     *
     * @var array
     */
    public static $inclusionsClassmap = [];

    /**
     * Path to mapfile.
     *
     * @var string
     */
    public static $mapfile = '';

    /**
     * Constructor.
     *
     * Registers the autoload() method in the SPL autoloader stack.
     */
    public function __construct()
    {
        spl_autoload_register([$this, 'autoload'], true, true);
    }

    /**
     * Autoloads a Class.
     *
     * @param string $classname The name of the class
     *
     * @return bool True on successful class loading, false otherwise.
     */
    public static function autoload($classname)
    {
        // stop early, if class or interface or trait already loaded
        if (class_exists($classname, false) || interface_exists($classname, false)) {
            return false;
        }

        // stop early, if trait already loaded (PHP 5.4)
        if (function_exists('trait_exists') && trait_exists($classname, false)) {
            return false;
        }

        /*
         * if the classname is to exclude, then
         * 1) stop autoloading immediately by
         * returning false, to save any pointless processing
         */
        if (self::autoloadExclusions($classname)) {
            return false;
        }

        /*
         * try to load the file by searching the
         * 2) hardcoded mapping table
         *
         * Note: If classname was included, autoloadInclusions returns true.
         */

         return true === self::autoloadInclusions($classname);

        /*
         * try to load the file by searching the
         * 3) automatically created mapping table.
         *
         * Note: the mapping table is loaded from APC or file.
         */

        return true === self::autoloadByApcOrFileMap($classname);

        /*
         * Try to load the file via include path lookup.
         * 5) psr-0 loader
         *
         * Note: If the file is found, it's added to the mapping file.
         * The next time the file is requested, it will be loaded
         * via the method above (3)!
         */

        return true === self::autoloadIncludePath($classname);

        /*
         * If classname was not found by any of the above methods, it's an
         * 6) Autoloading Fail
         */

        return false;
    }

    /**
     * Excludes a certain classname from the autoloading.
     *
     * Some libraries have their own autoloaders, like e.g. Doctrine, Smarty.
     * In these cases Koch Framework has the first autoloader in the stack,
     * but is not responsible for loading.
     *
     * @param string $classname Classname to check for exclusion.
     *
     * @return bool true, if the class is to exclude.
     */
    public static function autoloadExclusions($classname)
    {
        // define parts of classnames for exclusion
        foreach (['Smarty_Internal', 'Smarty_', 'PHPUnit', 'PHP_CodeCoverage'] as $classnameToExclude) {
            if (str_contains($classname, $classnameToExclude)) {
                return true;
            }
        }

        // exlude Doctrine
        if (str_starts_with($classname, 'Doctrine')) {
            return true;
        }

        return false;
    }

    /**
     * Includes a certain classname by using a manually maintained autoloading map.
     *
     * @param string $classname Classname to check for inclusion.
     *
     * @return bool if classname was included
     */
    public static function autoloadInclusions($classname)
    {
        // check if classname is in autoloading map
        if (isset(self::$inclusionsClassmap[$classname])) {
            include self::$inclusionsClassmap[$classname];

            return true;
        }

        return false;
    }

    /**
     * Loads a file by classname using the autoloader mapping array from file or apc.
     *
     * @param string $classname The classname to look for in the autoloading map.
     *
     * @return bool True on file load, otherwise false.
     */
    public static function autoloadByApcOrFileMap($classname)
    {
        if (empty(self::$autoloaderMap)) {
            if (defined('APC') and APC  === true) {
                self::$autoloaderMap = self::readAutoloadingMapApc();
            } else { // load the mapping from file
                self::$autoloaderMap = self::readAutoloadingMapFile();
            }
        }

        if (isset(self::$autoloaderMap[$classname])) {
            include_once self::$autoloaderMap[$classname];

            return true;
        }

        return false;
    }

    /**
     * PSR-0 Loader.
     *
     * - hardcoded namespaceSeparator
     * - hardcoded extension
     *
     * @link https://www.php-fig.org/psr/psr-0/
     * @link http://gist.github.com/221634
     *
     * @param string $classname
     *
     * @return bool True on success of require, false otherwise.
     */
    public static function autoloadIncludePath($classname)
    {
        #echo "Class requested $classname <br>";

        // trim opening namespace separator
        $classname = ltrim($classname, '\\');

        $filename = '';

        // determine position of last namespace separator
        if (false !== ($lastNsPos = strripos($classname, '\\'))) {
            // everything before it, is the namespace
            $namespace = substr($classname, 0, $lastNsPos + 1);
            // everything after it, is the classname
            $classname = substr($classname, $lastNsPos + 1);
            // replace every namespace separator with a directory separator
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        }

        // convert underscore
        $filename .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';

        #echo "$classname => $filename <br>";

        // searches on include path for the file and returns absolute path
        $filename = stream_resolve_include_path($filename);

        #echo "$classname => $filename => $namespace<br>";

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
            } catch (\Exception) {
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

        spl_autoload_register([self::class, 'autoload'], true, true);
    }

    /**
     * Unregisters the autoloader.
     */
    public static function unregister()
    {
        spl_autoload_unregister([self::class, 'autoload']);
    }
}