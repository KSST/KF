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

namespace Koch\Module;

/**
 * \Koch\Module\ManifestManager
 */
class ManifestManager
{
    /**
     * @var array contains the module informations
     */
    private static $modulesInfo = false;

    /**
     * @var array contains the system-wide module registry
     */
    private static $modulesRegistry  = false;

    /**
     * Setter for module infos
     *
     * @param array $module_infos_array
     */
    public static function setModuleInformations($module_infos_array)
    {
        self::$modulesInfo = $module_infos_array;
    }

    /**
     * Setter for modules registry
     *
     * @param array $module_registry_array
     */
    public static function setModuleRegistry($module_registry_array)
    {
        self::$modulesRegistry = $module_registry_array;
    }

    /**
     * Reads the CMS Module Registry
     *
     * This is the right method if you want to know if
     * a module is installed and active or deactivated.
     *
     * @return array Module Registry Array
     */
    public static function readModuleRegistry()
    {
        return \Koch\Config\Config()->read(APPLICATION_PATH . 'configuration/modules.config.php');
    }

    /**
     * Writes the Module Registry
     *
     * @param  array $array The Module Registry Array to write.
     * @return bool
     */
    public static function writeModuleRegistry($array)
    {
        return \Koch\Config\Config()->write(APPLICATION_PATH . 'configuration/modules.config.php');
    }

    /**
     * Returns the module configuration as array
     *
     * @param  string $modulename
     * @return array  Module Configuration Array
     */
    public static function readModuleConfig($modulename)
    {
        return \Koch\Config\Config()->readModuleConfig($modulename);
    }

    /**
     * Checks if a modulename belongs to the core modules.
     *
     * @param  string  $modulename The modulename
     * @return boolean True if modulename is a core module, false otherwise.
     */
    public static function isCoreModule($modulename)
    {
        // hardcoded map with core modules
        static $core_modules = array( 'account', 'categories', 'controlcenter', 'doctrine', 'menu', 'modulemanager',
                                      'users', 'settings', 'systeminfo', 'thememanager', 'templatemanager');

        // @todo extract from module info file if core module or not
        return in_array($modulename, $core_modules);
    }

    /**
     * Get a list of all the module directories
     *
     * @return array
     */
    public static function getModuleDirectories()
    {
        return glob(APPLICATION_MODULES_PATH . '[a-zA-Z]*', GLOB_ONLYDIR);
    }

    /**
     * Get a list of all the module names
     *
     * 4 in 1 method, handling the following cases:
     * 1. array with module names
     * 2. named array with modulenames
     * 3. array with module names and paths
     * 4. named array with modulenames and paths
     *
     * @param  boolean $only_modulenames Toggle between only_names (true) and names+paths.
     * @param  boolean $named_array      Toggle between named (true) and unnamed array.
     * @return array(  $modulename => $module_path )
     */
    public static function getModuleNames($named_array = false, $only_modulenames = false)
    {
        $modules = array();

        $module_dirs = self::getModuleDirectories();

        foreach ($module_dirs as $module_path) {
            // strip path off
            $modulename = str_replace(APPLICATION_MODULES_PATH, '', $module_path);

            if ($only_modulenames === true) {
                if ($named_array === false) {
                    $modules[] = $modulename;
                } else {
                    $modules[] = array('name' => $modulename);
                }
            } else {
                if ($named_array === false) {
                    $modules[] = array($modulename => $module_path );
                } else {
                    $modules[] = array('name' => $modulename, 'path' => $module_path);
                }
            }
        }

        return $modules;
    }

    /**
     * Returns all activated modules
     *
     * @return array $activated_modules_array
     */
    public static function getAllActivatedModules()
    {
        $activated_modules_array = array();

        $modules = self::getModuleNames(true);

        foreach ($modules as $module) {
            if (true === self::isModuleActive($module)) {
                $activated_modules_array[$module] = self::$modulesRegistry[$module];
            }
        }

        return $activated_modules_array;
    }

    /**
     * Checks if a module is active or deactived.
     *
     * @param boolean $module True if module activated, false otherwise.
     */
    public static function isModuleActive($module)
    {
        // load module registry, if not available yet
        if (empty(self::$modulesRegistry[$module])) {
            self::$modulesRegistry = self::readModuleRegistry();
        }

        // check, if the module is
        if (isset(self::$modulesRegistry[$module]['active']) and self::$modulesRegistry[$module]['active'] == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches all pieces of information of a certain module
     *
     * @param  string $module
     * @return array  Module Informations
     */
    public static function getModuleInformations($module = null)
    {
        $modulename = strtolower($module);

        // check if the infos of this specific module were catched before
        if (self::$modulesInfo[$modulename] !== null) {
            return self::$modulesInfo[$modulename];
        }

        // fetch infos for the requested $module
        return self::loadModuleInformations($module);
    }

    public static function buildModuleRegistry()
    {
        foreach ($module_directories as $module_path) {
            // strip off path info and get the modulename
            $modulename = str_replace(APPLICATION_MODULES_PATH, '', $module_path);
        }

        self::writeModuleRegistry();
    }

    /**
     * Gather Module Informations from Manifest Files
     *
     * @staticvar array $modulesinfo
     * @param  mixed array|string $module array with modulenames or one modulename
     * @return moduleinformations (self::$modulesinfo)
     */
    public static function loadModuleInformations($module = null)
    {
        // Init vars
        $module_directories = array();
        $number_of_modules = 0;

        /**
         * either fetch the module requested via parameter $module
         * fetch all modules
         */
        if ($module === null) {
            $module_directories = self::getModuleDirectories();
        } else {
            // cast string to array
            $module_directories[] = APPLICATION_MODULES_PATH . $module;
        }

        foreach ($module_directories as $modulepath) {
            /**
             * create array with pieces of information about a module
             */

            // 1) get the modulename, by stripping off the path info
            $modulename = str_replace(APPLICATION_MODULES_PATH, '', $modulepath);

            self::$modulesinfo[$modulename]['name']   = $modulename;
            self::$modulesinfo[$modulename]['id']     = $number_of_modules;
            self::$modulesinfo[$modulename]['path']   = $modulepath;
            self::$modulesinfo[$modulename]['core']   = self::isCoreModule($modulename);

            // active - based on /configuration/modules.config.php
            self::$modulesinfo[$modulename]['active'] = self::isModuleActive($modulename);

            // hasMenu / ModuleNavigation
            self::$modulesinfo[$modulename]['menu'] = is_file($modulepath . '/' . $modulename . '.menu.php');

            // hasInfo
            $module_infofile = $modulepath . '/' . $modulename . '.info.php';
            $config = new \Koch\Config\Config;

            if (is_file($module_infofile) === true) {
                #\Koch\Debug\Debug::firebug($module_infofile);

                self::$modulesinfo[$modulename]['info'] = $config->read($module_infofile);
            } else { // create file in DEV MODE
                // if the info file for a module does not exists yet, create it
                $config->write($module_infofile);
            }

            // hasRoutes

            // hasConfig
            $config = self::readModuleConfig($modulename);
            if ($config[$modulename] !== null) {
                self::$modulesinfo[$modulename]['config'] = $config[$modulename];

                // properties
                if (isset($config['properties'])) {
                    self::$modulesinfo[$modulename]['settings'] = $config['properties'];
                }
            }
            /* else {
              $modules[$modulename]['config'] = $config;
              } */

            // hasLanguages
            self::$modulesinfo[$modulename]['languages'] = self::getLanguageInfosForModule($modulepath);

            // take some stats: increase the module counter
            self::$modulesinfo['yy_summary']['counter'] = ++$number_of_modules;
        }

        ksort(self::$modulesinfo);

        #\Koch\Debug\Debug::printR(self::$modulesinfo);

        return self::$modulesinfo;
    }

    public static function getLanguageInfosForModule($modulepath)
    {
        $lang = array();

        // we are looking at the languages folder for the given module path
        $moduleLangDir = $modulepath . DIRECTORY_SEPARATOR . 'languages';

        // return early, if languages directory does not exist
        if (false === is_dir($moduleLangDir)) {
            return 'No language dir.';
        }

        // lets recurse this directory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($moduleLangDir, \FilesystemIterator::UNIX_PATHS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        // some leaves found (dirs and files)
        foreach ($iterator as $file) {
            // proceed with iteration instantly, if file is not a gettext file
            if (0 === preg_match('/.(mo|po)$/', $file->getFileName())) {
                 continue;
            }

            // fetch locale from path (en_UK, de_DE)
            if (1 === preg_match('/[a-z]{2}_[A-Z]{2}/', $file->getPathName(), $match)) {
                $locale = $match[0];
            }

            // fetch file extension (mo|po)
            if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
                $ext = $file->getExtension();
            } else { // php lower then 5.3.6
                $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            }

            $isReadable = $file->isReadable() ? 'r' : '';
            $isWritable = $file->isWritable() ? 'w' : '';

            /**
             * Add some more pieces of information about the file
             */
            $lang[$locale][$ext]['pathName'] = realpath($file->getPathName());
            $lang[$locale][$ext]['fileName'] = $file->getFileName();
            $lang[$locale][$ext]['filePermString'] = self::filePermissions($lang[$locale][$ext]['pathName']);
            $lang[$locale][$ext]['fileReadable'] = $file->isReadable();
            $lang[$locale][$ext]['fileWriteable'] = $file->isWritable();
            $lang[$locale][$ext]['timestamp'] = date(DATE_FORMAT, $file->getCTime());
            $lang[$locale][$ext]['cssClass'] = '-' . $isReadable . $isWritable;

        }

        /**
         * Add some more pieces of information about the locale
         */

         // if the language definitions are not already loaded, load them
        if (empty(self::$l10n_sys_locales)) {
            // fetch arrays containing locale data
            require __DIR__ . '/../Localization/Locales.php';
            self::$l10n_sys_locales = $l10n_sys_locales;
        }

        foreach ($lang as $locale => $filedata) {
            // get more data about that locale from the locales array
            if (isset(self::$l10n_sys_locales[$locale]) == true) {
                $lang[$locale]['country_www']   = self::$l10n_sys_locales[$locale]['country-www'];
                $lang[$locale]['lang_native']   = self::$l10n_sys_locales[$locale]['lang-native'];
                $lang[$locale]['lang_www']      = self::$l10n_sys_locales[$locale]['lang-www'];
                $lang[$locale]['lang']          = self::$l10n_sys_locales[$locale]['lang'];
            } else { // locale not in locales array
                $lang[$locale]['country_www']   = 'unknown';
                $lang[$locale]['lang_native']   = '<em>locale: </em>' . $locale;
                $lang[$locale]['lang_www']  = '';
                $lang[$locale]['lang']   = $locale;
            }
        }

        #\Koch\Debug\Debug::printR($langinfo);

        return $lang;
    }

    /**
     * Returns file permissions as string
     *
     * @staticvar array $permissions
     * @param  type   $filename
     * @return string File Permissions as string, e.h. "rwx", "rw-"
     */
    private static function filePermissions($filename)
    {
        static $permissions = array("---", "--x", "-w-", "-wx", "r--", "r-x", "rw-", "rwx");
        $perm_oct = substr(decoct(fileperms($filename)), 3);

        $string = '[';
        $string .= $permissions[(int) $perm_oct[0]] . '|';
        $string .= $permissions[(int) $perm_oct[1]] . '|';
        $string .= $permissions[(int) $perm_oct[2]] . ']';

        return $string;
    }
}
