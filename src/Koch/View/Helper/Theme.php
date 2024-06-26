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

namespace Koch\View\Helper;

use Koch\View\AbstractRenderer;

/**
 * Class for handling of Themes.
 *
 * This class provides abstracted (object) access to a theme's theme_info.xml file.
 */
class Theme
{
    public $theme      = '';
    public $theme_info = [];

    /**
     * Constructor, or what ;).
     *
     * @param string $theme Name of the Theme.
     *
     * @return \Koch\View\Helper\Theme
     */
    public function __construct($theme)
    {
        $this->setThemeName($theme);

        $this->getInfoArray($theme);

        return $this;
    }

    /**
     * @param string $theme
     */
    public function setThemeName($theme)
    {
        if ($theme === null) {
            throw new \InvalidArgumentException('Please  provide a theme name.');
        }

        $this->theme = $theme;
    }

    /**
     * Getter for the "theme_info.xml" file of the currently activated theme.
     *
     * @return string Filepath to "theme_info.xml" of the currently activated theme.
     */
    public function getCurrentThemeInfoFile()
    {
        // get array for frontend or backend theme
        $paths = AbstractRenderer::getThemeTemplatePaths();

        foreach ($paths as $path) {
            $file = $path . DIRECTORY_SEPARATOR . 'theme_info.xml';

            if (is_file($file)) {
                return $file;
            }
        }
    }

    /**
     * Looks for the requested theme in the frontend and backend theme folder
     * and returns the theme path.
     *
     * @param string $theme Theme name.
     *
     * @return string Path to theme.
     */
    public function getPath($theme = null)
    {
        if ($theme === null) {
            $theme = $this->getName();
        }

        if (is_dir(APPLICATION_PATH . 'Themes') === false) {
            throw new \RuntimeException('The application themes folder was not found.');
        }

        $frontend = APPLICATION_PATH . 'Themes/frontend/' . $theme . DIRECTORY_SEPARATOR;
        if (is_dir($frontend)) {
            return $frontend;
        }

        $backend = APPLICATION_PATH . 'Themes/backend/' . $theme . DIRECTORY_SEPARATOR;
        if (is_dir($backend)) {
            return $backend;
        }

        return false;
    }

    /**
     * Looks for the requested theme in the frontend and backend theme folder
     * and returns the web path of the theme.
     *
     * @param string $theme Theme name.
     *
     * @return string Webpath of theme (for usage in templates).
     */
    public function getWebPath($theme = null)
    {
        if ($theme === null) {
            $theme = $this->getName();
        }

        // check absolute, return www
        if (is_dir(APPLICATION_PATH . 'Themes/frontend/' . $theme)) {
            return WWW_ROOT_THEMES_FRONTEND . $theme . '/';
        }

        // check absolute, return www
        if (is_dir(APPLICATION_PATH . 'Themes/backend/' . $theme)) {
            return WWW_ROOT_THEMES_BACKEND . $theme . '/';
        }
    }

    /**
     * Returns "theme_info.xml" for the requested theme.
     *
     * @param string $theme Theme name.
     *
     * @return string File path to "theme_info.xml" file.
     *
     * @throws \Koch\Exception\Exception
     */
    public function getThemeInfoFile($theme)
    {
        $file = $this->getPath($theme) . 'theme_info.xml';

        if (is_file($file)) {
            return $file;
        }

        throw new \Exception('The Theme "' . $theme . '" has no "theme_info.xml" file.');
    }

    /**
     * Returns Theme Infos as array.
     *
     * @param string $theme Name of the Theme.
     *
     * @return array Theme_Info.xml as Array.
     */
    public function getInfoArray($theme = null)
    {
        $file = $this->getThemeInfoFile($theme);

        $array = \Koch\Config\Adapter\XML::read($file);

        // when setting array as object property remove the inner theme array
        $this->theme_info = $array['theme'];

        return $this->theme_info;
    }

    /**
     * --------------------------------------------------------------------------------------------
     *  GETTERS
     * --------------------------------------------------------------------------------------------.
     */

    /**
     * Gets shortname or folder name.
     *
     * @return string short name / folder name.
     */
    public function getName()
    {
        return $this->theme;
    }

    public function getFullName()
    {
        return $this->theme_info['name'];
    }

    public function getAuthor()
    {
        return $this->theme_info['authors'];
    }

    public function getVersion()
    {
        return $this->theme_info['theme_version'];
    }

    public function getRequiredVersion()
    {
        return $this->theme_info['required_version'];
    }

    public function getDate()
    {
        return $this->theme_info['date'];
    }

    public function getLayout()
    {
        return $this->theme_info['layout'];
    }

    public function getCss()
    {
        return $this->theme_info['css'];
    }

    public function getLayoutFile()
    {
        if ($this->theme_info['layout']['mainfile'] !== null) {
            #return $this->getPath() . $this->theme_info['layout']['mainfile'];

            return $this->theme_info['layout']['mainfile'];
        } elseif (false === isset($this->theme_info['layout']['mainfile'])) {
            // maybe we have a main template css file named after the theme
            // $layout_file = $this->getPath() . $this->getName() . '.tpl';
            $layout_file = $this->getName() . '.tpl';

            if (is_file($layout_file)) {
                return $layout_file;
            }
        } else { // no main layout found !
            throw new \Exception('No Layout File defined. Check ThemeInfo File of ' . $this->getName(), 9090);
        }
    }

    public function getRenderEngine()
    {
        return $this->theme_info['renderengine'];
    }

    public function isBackendTheme()
    {
        return (bool) $this->theme_info['backendtheme'];
    }

    public function isFrontendTheme()
    {
        return (bool) $this->theme_info['backendtheme'] === true ? false : true;
    }

    public function getArray()
    {
        return $this->theme_info;
    }

    public static function getThemeDirectories()
    {
        return array_merge(
            self::iterateDir(APPLICATION_PATH . 'themes/frontend/', 'frontend'),
            self::iterateDir(APPLICATION_PATH . 'themes/backend/', 'backend')
        );
    }

    /**
     * Iterates over a theme dir (backend / frontend) and fetches some data.
     *
     * @param string $dir             APPLICATION_FRONTEND_THEMES_PATH, APPLICATION_BACKEND_THEMES_PATH
     * @param string $type            'frontend' or 'backend'
     * @param bool   $only_index_name
     *
     * @return string
     */
    protected static function iterateDir($dir, $type, $only_index_name = true)
    {
        $dirs    = '';
        $dir_tmp = '';
        $i       = 0;
        $themes  = [];

        $dirs = new \DirectoryIterator($dir);

        foreach ($dirs as $dir) {

            $dir_tmp = $dir->getFilename();

            // Skip early on dots
            if (in_array($dir_tmp, ['.', '..', '.git', 'vendor'], true)) {
                continue;
            }

            /*
             * take only directories in account, which contain a "theme_info.xml" file
             */
            if (is_file($dir->getPathName() . DIRECTORY_SEPARATOR . 'theme_info.xml')) {
                $i = $i + 1;

                if ($only_index_name === false) {
                    // add fullpath
                    $themes[$i]['path'] = $dir->getPathName();

                    // set frontend as type
                    $themes[$i]['type'] = $type;

                    // add dirname
                    $themes[$i]['name'] = $type . DIRECTORY_SEPARATOR . $dir;
                } else {
                    // add dirname
                    $themes[$i] = $type . DIRECTORY_SEPARATOR . $dir;
                }
            }
        }

        unset($i, $dirs, $dir_tmp);

        return $themes;
    }
}
