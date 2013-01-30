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

namespace Koch\Tools;

/**
 * Koch Framework - CSS Builder
 *
 * It's a tool class for merging the modularized css files of the Koch CSS Framework
 * into one file, the "import.css". If you include "import.css" in your Theme you laying
 * a solid base for your own CSS style modifications on top of our CSS Framework.
 * Plus, you might still modify the Koch Framework - CSS Framework Core to your intents.
 *
 * Basic Usage:
 *   $builder = new Koch_Cssbuilder();
 *   $builder->setBuilderOptions( $array );
 *   $builder->build();
 *
 * Optional:
 *   $builder->addBrowser( 'ie', 'Internet Explorer', true, 'ie' );
 */
class CssBuilder
{
    /**
     * Builder Version
     */
    protected static $generatorName = 'CSS-Builder';
    protected static $generatorVersion = '1.0.5';
    protected static $generatorVersionDate = '2011/04/29';

    /**
     * Contains the Builder informations
     *
     *  $_configuration[info]  contains:
     *               [generator]   = <generator name>
     *               [version]      = <generator version>
     *               [date]          = <version date>
     *
     *  $_configuration[core]  contains:
     *               [compile]     = true|false
     *               [import]       = true|false
     *
     *  $_configuration[frontend]  contains:
     *               [compile]     = true|false
     *               [path]          = <frontend path>
     *               [theme]       = <theme>
     *
     *  $_configuration[backend]  contains:
     *               [compile]     = true|false
     *               [path]          = <backend path>
     *               [theme]       = <theme>
     *
     *  $_configuration[browsers]  contains:
     *               [default]
     *                     [description]   = Standard Browser (Mozilla)
     *                     [active]          = true|false
     *                     [postfix]         =
     *               [ie]
     *                     [description]   = Internet Explorer
     *                     [active]          = true|false
     *                     [postfix]         = ie
     *               [chrome]
     *                     [description]   = Google Chrome
     *                     [active]          = true|false
     *                     [postfix]         = chrome
     *
     * @var array
     */
    private static $configuration = array();

    /**
     * Contains the Browser informations
     * @var array
     */
    private static $browsers = array();

    /**
     * frontend theme
     * @var string
     */
    private static $frontendTheme;

    /**
     * backend theme
     * @var string
     */
    private static $backendTheme;

    /**
     * path to the frontend directory
     * @var string
     */
    private static $frontendPath;

    /**
     * path to the backend directory
     * @var string
     */
    private static $backendPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        // initializing
        $config = Clansuite_CMS::getClansuiteConfig();

        self::setFrontendPath(APPLICATION_PATH . 'themes/frontend/');
        self::setBackendPath(APPLICATION_PATH . 'themes/backend/');
        self::setFrontendTheme($config['template']['frontend_theme']);
        self::setBackendTheme($config['template']['backend_theme']);
        self::addBrowser('default', 'Standard Browser (Mozilla)', true, '');
    }

    /**
     * Compiler
     */
    public function build($index)
    {
        $config = self::getConfiguration();

        $coreadditionalFiles = $themeadditionalFiles = $themeBackadditionalFiles = array();

        // build browser specific configuration filename
        $browser = $config['browsers'];
        $postfix = $browser[$index]['postfix'];
        if ($postfix != '') {
            $postfix = '_' . $postfix;
        }
        $builderINI = 'cssbuilder' . $postfix . '.ini';

        // Read Core INI-File
        $coreINI = APPLICATION_PATH . 'themes/core/css/csfw/' . $builderINI;
        $coreInfo = $this->readCssBuilderIni($coreINI);
        $coreCssName = $coreInfo['cssname'] . $postfix . '.css';

        /* ----- prepare core ----- */
        /* Core-Info */
        $corePath = APPLICATION_PATH . $coreInfo['path'];
        $coreFiles = explode(',', $coreInfo['files']);
        if (strlen($coreInfo['additionalFiles']) > 0) {
            $coreadditionalFiles = explode(',', $coreInfo['additionalFiles']);
        }

        $core_compact = $this->getCoreCompactHeader($coreInfo);

        /**
         * prepare frontend theme
         */
        if (true === $config['compileThemeFrontend']) {
            if (mb_substr($config['themeFrontendPath'], strlen($config['themeFrontendPath']) - 1) == '/' ||
               mb_substr($config['themeFrontendPath'], strlen($config['themeFrontendPath']) - 1) == '\/') {
                $config['themeFrontendPath'] = mb_substr(
                    $config['themeFrontendPath'],
                    strlen($config['themeFrontendPath']) - 1
                );
            }

            // Read INI-File
            $themeINI = $config['themeFrontendPath'] . DIRECTORY_SEPARATOR
                . $config['themeFrontend'] .  '/css/' . $builderINI;
            $themeInfo = $this->readCssBuilderIni($themeINI);
            $themeInfo['path'] = str_replace("{theme}", $config['themeFrontend'], $themeInfo['path']);

            /* Theme-Info */
            $themePath = APPLICATION_PATH . $themeInfo['path'];
            $themeCssName = $themeInfo['cssname'] . $postfix . '.css';
            $themeFiles = explode(',', $themeInfo['files']);
            if (strlen($themeInfo['additionalFiles']) > 0) {
                $themeadditionalFiles = explode(',', $themeInfo['additionalFiles']);
            }

            $theme_compact = $this->getThemeCompactHeader($themeInfo);
        }

        #\Koch\Debug\Debug::printR( $themeFiles );

        /**
         * prepare backend theme
         */
        if (true === $config['compileThemeBackend']) {
            if (mb_substr($config['themeBackendPath'], strlen($config['themeBackendPath']) - 1) == '/' ||
                    mb_substr($config['themeBackendPath'], strlen($config['themeBackendPath']) - 1) == '\/')
            {
                $config['themeBackendPath'] = mb_substr(
                    $config['themeBackendPath'],
                    strlen($config['themeBackendPath']) - 1
                );
            }
            // Read INI-File
            $themeBackINI = $config['themeBackendPath'] . DIRECTORY_SEPARATOR
                . $config['themeBackend'] . '/css/' . $builderINI;
            $themeBackInfo = $this->readCssBuilderIni($themeBackINI);
            $themeBackInfo['path'] = str_replace("{theme}", $config['themeBackend'], $themeBackInfo['path']);

            /* Theme-Info */
            $themeBackPath = APPLICATION_PATH . $themeBackInfo['path'];
            $themeBackCssName = $themeBackInfo['cssname'] . $postfix . '.css';
            $themeBackFiles = explode(',', $themeBackInfo['files']);

            if (strlen($themeBackInfo['additionalFiles']) > 0) {
                $themeBackadditionalFiles = explode(',', $themeBackInfo['additionalFiles']);
            }

            $themeBack_compact = $this->getThemeCompactHeader($themeBackInfo);
        }

        $_compact = $this->getCssFileHeader($browser[$index]['description']);
        $html = '';

        /**
         *  Build Core CSS
         */
        if (true === $config['compileCore']) {
            $_comp = '';
            $_comp .= $core_compact;

            foreach ($coreFiles as $filename) {
                $content = self::loadStylesheet($corePath . $filename, true);
                $_comp .= "/* [" . basename($filename) . "] */" . CR;
                $_comp .= $content . CR;
            }

            $this->saveStylesheet($corePath . $coreCssName, $_comp);

            $html .= '<p class="cmBoxMessage" style="padding-left:50px;"><b>Core Import File:</b>';
            $html .= '&nbsp;&nbsp;' . $corePath;
            $html .= '<span class="cmSuccessFilenameColor"><b>' . $coreCssName . '</b></span> wurde generiert</p>';
        }

        /**
         * Build Frontend Theme CSS
         */
        /**
         * create Info + compiled frontend theme stylesheet
         */
        if (true === $config['compileThemeFrontend']) {
            $coreImp = '.';
            $_comp = $_compact;

            if (true === $config['coreImport']) {
                $_comp .= "/** [Core Import] */\n";
                $_comp .= "@import url('../../../core/css/" . $coreCssName . "');\n\n";
                $coreImp = ' und die Core importiert.';
            }

            if (count($coreadditionalFiles) > 0) {
                foreach ($coreadditionalFiles as $filename) {
                    $_comp .= "/* Import additional file: [" . trim($filename) . "] */" . CR;
                    $_comp .= "@import url('" . trim($filename) . "');\n\n";
                }
            }

            if (count($themeadditionalFiles) > 0) {
                foreach ($themeadditionalFiles as $filename) {
                    $_comp .= "/* Import additional file: [" . trim($filename) . "] */" . CR;
                    $_comp .= "@import url('" . trim($filename) . "');\n\n";
                }
            }

            $_comp .= $theme_compact;

            foreach ($themeFiles as $filename) {
                $content = self::loadStylesheet($themePath . $filename, true);
                $_comp .= "/* [" . basename($filename) . "] */" . CR;
                $_comp .= $content . CR;
            }

            $this->saveStylesheet($themePath . $themeCssName, $_comp);

            $html .= '<p class="cmBoxMessage" style="padding-left:50px;"><b>Frontend Theme Import File:</b>';
            $html .= '&nbsp;&nbsp;' . $themePath;
            $html .= '<span class="cmSuccessFilenameColor">';
            $html .= '<b>' . $themeCssName . '</b>';
            $html .= '</span> wurde generiert' . $coreImp . '</p>';
        }

        /**
         *  Build Backend Theme CSS
         */

        /**
         * create Info + compiled backend theme stylesheet
         */
        if (true === $config['compileThemeBackend']) {
            $coreImp = '.';
            $_comp = $_compact;

            if (true === $config['coreImport']) {
                $_comp .= "/** [Core Import] */\n";
                $_comp .= "@import url('../../../core/css/" . $coreCssName . "');\n\n";
                $coreImp = ' und die Core importiert.';
            }

            if (count($coreadditionalFiles) > 0) {
                foreach ($coreadditionalFiles as $filename) {
                    $_comp .= "/* Import additional file: [" . trim($filename) . "] */" . CR;
                    $_comp .= "@import url('" . trim($filename) . "');\n\n";
                }
            }

            if (count($themeBackadditionalFiles) > 0) {
                foreach ($themeBackadditionalFiles as $filename) {
                    $_comp .= "/* Importing additional css file: [" . trim($filename) . "] */" . CR;
                    $_comp .= "@import url('" . trim($filename) . "');\n\n";
                }
            }

            $_comp .= $themeBack_compact;

            foreach ($themeBackFiles as $filename) {
                $content = self::loadStylesheet($themeBackPath . $filename, true);
                $_comp .= "/* [" . basename($filename) . "] */" . CR;
                $_comp .= $content . CR;
            }

            $this->saveStylesheet($themeBackPath . $themeBackCssName, $_comp);

            $html .= '<p class="cmBoxMessage" style="padding-left:50px;"><b>Backend Theme Import File:</b>';
            $html .= '&nbsp;&nbsp;' . $themeBackPath;
            $html .= '<span class="cmSuccessFilenameColor"><b>' . $themeBackCssName . '</b></span> wurde generiert' . $coreImp . '</p>';
        }

        return $html;
    }

    /**
     * Reads the ini file
     *
     * @return array ini array
     */
    protected function readCssBuilderIni($inifile)
    {
        $iniArray = parse_ini_file($inifile);
        #\Koch\Debug\Debug::printR( $iniArray );

        // replacements
        $search = array(' ', "\t", "\r\n", "\r", CR);
        $replace = array('', '', '', '', '', '');
        $iniArray['files'] = str_replace($search, $replace, $iniArray['files']);

        if (mb_substr($iniArray['files'], strlen($iniArray['files']) - 1) == ',') {
            $iniArray['files'] = mb_substr($iniArray['files'], 0, strlen($iniArray['files']) - 1);
        }

        return $iniArray;
    }

    /**
     * Returns the header text for the css file.
     *
     * @param  string $browserInfo Name of the browser this css file is written for.
     * @return string Header text for the css file.
     */
    protected function getCssFileHeader($browserInfo = '')
    {
        $h  = '';
        $h .= '/**' . CR;
        $h .=  ' * --------------------------------------------------------------------------------' . CR;
        $h .=  ' * Koch CSS Framework' . CR;
        $h .=  ' * --------------------------------------------------------------------------------' . CR;
        $h .=  ' * This file has been auto-generated. Its a compilation of several css files.' . CR;
        $h .=  ' * Do not edit manually. Use the CssBuilder instead!' . CR;
        $h .=  ' * Last Updated:  ' . date('Y-m-d H:i:s', time()) . CR;
        $h .=  ' * --------------------------------------------------------------------------------' . CR;
        $h .=  ' * @description  Created for - ' . $browserInfo . CR;
        $h .=  ' * --------------------------------------------------------------------------------' . CR;
        $h .=  ' */' . CR;
        $h .=  CR;
        $h .=  '@charset "UTF-8";' . CR;
        $h .=  CR;
        $h .=  CR;

        return $h;
    }

    /**
     * Returns the header text for the core css file.
     *
     * @param  array  $coreInfo Array version information about this class.
     * @return string Header text for the core css file.
     */
    protected function getCoreCompactHeader($coreInfo = '')
    {
        $header  = '';
        $header  = '/**' . CR;
        $header .= ' * This file has been auto-generated. Its a compilation of several css files.' . CR;
        $header .= ' * Do not edit manually. Use the CssBuilder!' . CR;
        $header .= ' * Last Update:  ' . date('Y-m-d H:i:s', time()) . CR;
        $header .= ' * --------------------------------------------------------------------------------' . CR;
        $header .= ' * Framework:    ' . $coreInfo['framework'] . CR;
        $header .= ' * Description:  ' . $coreInfo['description'] . CR;
        $header .= ' * Author:       ' . $coreInfo['author'] . CR;
        $header .= ' * Version:      ' . $coreInfo['version'] . CR;
        $header .= ' * Version-Date: ' . $coreInfo['date'] . CR;
        $header .= ' * --------------------------------------------------------------------------------' . CR;
        $header .= ' * @generated' . CR;
        $header .= ' */' . CR;

        return $header;
    }

    /**
     * Returns the header text for the theme css file.
     *
     * @param  array  $themeInfo Array with version information about the theme.
     * @return string Header text for the theme css file.
     */
    protected function getThemeCompactHeader($themeInfo = '')
    {
        $header  = '';
        $header  = '/**' . CR;
        $header .= ' * This file has been auto-generated. Its a compilation of several css files. ' . CR;
        $header .= ' * Do not edit manually. Use CssBuilder!' . CR;
        $header .= ' * Last Update:  ' . date('Y-m-d H:i:s', time()) . CR;
        $header .= ' * --------------------------------------------------------------------------------' . CR;
        $header .= ' * Framework:    ' . $themeInfo['framework'] . CR;
        $header .= ' * Description:  ' . $themeInfo['description'] . CR;
        $header .= ' * Author:       ' . $themeInfo['author'] . CR;
        $header .= ' * Version:      ' . $themeInfo['version'] . CR;
        $header .= ' * Version-Date: ' . $themeInfo['date'] . CR;
        $header .= ' * --------------------------------------------------------------------------------' . CR;
        $header .= ' * @generated' . CR;
        $header .= ' */' . CR;

        return $header;
    }

    /**
     * Save stylesheet to import file
     *
     * @param  type    $comp_filename
     * @param  type    $_compact
     * @return boolean
     */
    protected function saveStylesheet($comp_filename, $_compact)
    {
        if (!$filehandle = fopen($comp_filename, 'wb')) {
            echo _('Could not open file: ') . $comp_filename;

            return false;
        }

        if (fwrite($filehandle, $_compact) === false) {
            echo _('Could not write to file: ') . $comp_filename;

            return false;
        }
        fclose($filehandle);
    }

    /**
     * load_stylesheet
     *
     * @param $file The css file with the contents of the stylesheet.
     * @param $optimize (optional) Boolean whether CSS contents should be minified. Defaults to FALSE
     */
    protected static function loadStylesheet($file, $optimize = true)
    {
        $contents = '';

        if (file_exists($file) === true) {
            // Load the local CSS stylesheet.
            $contents = file_get_contents($file);

            // image path anpassen
            #$contents = str_replace('../images', 'images', $contents);
            $contents = str_replace('../../images', '../images', $contents);

            // Change to the current stylesheet's directory.
            $cwd = getcwd();
            chdir(dirname($file));

            // Process the stylesheet.
            $contents = self::loadStylesheetContent($contents, $optimize);

            // Change back directory.
            chdir($cwd);
        }

        return $contents;
    }

    /**
     * load_stylesheet_content
     *
     * Processes the content of a stylesheet for aggregation.
     *
     * @see Drupal v8, common.inc -> drupal_load_stylesheet_content()
     * @license GPL v2+
     *
     * @param $contents The contents of the stylesheet.
     * @param $optimize (optional) Boolean whether CSS contents should be minified. Defaults to FALSE
     */
    protected static function loadStylesheetContent($contents, $optimize = false)
    {
        // Remove multiple charset declarations for standards compliance (and fixing Safari problems).
        $contents = preg_replace('/^@charset\s+[\'"](\S*)\b[\'"];/i', '', $contents);

        if ($optimize === true) {
            // Perform some safe CSS optimizations.
            // Regexp to match comment blocks.
            $comment = '/\*[^*]*\*+(?:[^/*][^*]*\*+)*/';
            // Regexp to match double quoted strings.
            $double_quot = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
            // Regexp to match single quoted strings.
            $single_quot = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";
            // Strip all comment blocks, but keep double/single quoted strings.
            $contents = preg_replace(
                "<($double_quot|$single_quot)|$comment>Ss", "$1", $contents
            );
            // Remove certain whitespace.
            // There are different conditions for removing leading and trailing
            // whitespace.
            // @see http://php.net/manual/en/regexp.reference.subpatterns.php
            $contents = preg_replace('<
              // Strip leading and trailing whitespace.
                \s*([@{};,])\s*
              // Strip only leading whitespace from:
              // - Closing parenthesis: Retain "@media (bar) and foo".
              | \s+([\)])
              // Strip only trailing whitespace from:
              // - Opening parenthesis: Retain "@media (bar) and foo".
              // - Colon: Retain :pseudo-selectors.
              | ([\(:])\s+>xS',
                // Only one of the three capturing groups will match, so its reference
                // will contain the wanted value and the references for the
                // two non-matching groups will be replaced with empty strings.
                '$1$2$3', $contents
            );

            // End the file with a new line.
            $contents = trim($contents) . CR;
        }

        // Replaces @import commands with the actual stylesheet content.
        // This happens recursively but omits external files.
        $contents = preg_replace_callback(
            '/@import\s*(?:url\(\s*)?[\'"]?(?![a-z]+:)([^\'"\()]+)[\'"]?\s*\)?\s*;/',
            'self::load_stylesheet',
            $contents
        );

        return $contents;
    }

    /**
     * add browser
     *
     * @param $shortname string browser schortcut ( e.g. ie )
     * @param $description  string Browser information ( e.g. Internet Explorer )
     * @param $active  boolean browser is active for compile or not
     * @param $postfix string browser postfix for the import.css  ( e.g. 'ie' will generate import_ie.css )
     */
    public static function addBrowser($shortname, $description = '', $active = false, $postfix = '')
    {
        if (false === empty($shortname)) {
            $browserArray = self::getBrowsers();

            $browserArray[$shortname]['short'] = $shortname;
            $browserArray[$shortname]['description'] = $description;
            $browserArray[$shortname]['active'] = $active;
            $browserArray[$shortname]['postfix'] = $postfix;

            self::setBrowsers($browserArray);
        }
    }

    /**
     * =========================================
     * Getter methodes
     * =========================================
     */
    public static function getFrontendPath()
    {
        return self::$frontendPath;
    }

    public static function getBackendPath()
    {
        return self::$backendPath;
    }

    public static function getFrontendTheme()
    {
        return self::$frontendTheme;
    }

    public static function getBackendTheme()
    {
        return self::$backendTheme;
    }

    public static function getBrowsers()
    {
        return self::$browsers;
    }

    public static function getConfiguration()
    {
        return self::$configuration;
    }

    /**
     * =========================================
     * Setter methodes
     * =========================================
     */
    public static function setFrontendPath($value)
    {
        self::$frontendPath = $value;
    }

    public static function setBackendPath($value)
    {
        self::$backendPath = $value;
    }

    public static function setFrontendTheme($value)
    {
        self::$frontendTheme = $value;
    }

    public static function setBackendTheme($value)
    {
        self::$backendTheme = $value;
    }

    public static function setBrowsers($data)
    {
        self::$browsers = $data;
    }

    /**
     * BuilderInfo contains all definitions for the builder
     *
     * @param $config array Builder infos (paths, browser etc.)
     */
    public static function setConfiguration(array $config = null)
    {
        // initialize with default values when $options not declared
        if ($config === null) {
            $config['compileCore'] = false;
            $config['coreImport'] = true;

            $config['compileThemeFrontend'] = true;
            $config['themeFrontendPath'] = self::getFrontendPath();
            $config['themeFrontend'] = self::getFrontendTheme();

            $config['compileThemeBackend'] = false;
            $config['themeBackendPath'] = self::getBackendPath();
            $config['themeBackend'] = self::getBackendTheme();
            $config['browsers'] = self::getBrowsers();
        }

        $config['info']['generator_name'] = self::$generatorName;
        $config['info']['generator_version'] = self::$generatorVersion;
        $config['info']['generator_version_date'] = self::$generatorVersionDate;

        self::$configuration = $config;
    }
}
