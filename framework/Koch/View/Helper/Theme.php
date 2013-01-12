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
 *
 */

namespace Koch\View\Helper;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - Class for handling of Themes.
 *
 * This class provides abstracted (object) access to a theme's theme_info.xml file.
 */
class Theme
{
    public $theme = '';
    public $theme_info = array();

    /**
     * Constructor, or what ;)
     *
     * @param  string     $theme Name of the Theme.
     * @return Koch_Theme
     */
    public function __construct($theme)
    {
        $this->setThemeName($theme);

        $this->getInfoArray($theme);

        return $this;
    }

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

            if (is_file($file) === true) {
                return $file;
            }
        }
    }

    /**
     * Looks for the requested theme in the frontend and backend theme folder
     * and returns the theme path.
     *
     * @param  string $theme Theme name.
     * @return string Path to theme.
     */
    public function getPath($theme = null)
    {
        if ($theme === null) {
            $theme = $this->getName();
        }

        $frontend = APPLICATION_PATH . 'themes/frontend/' . $theme . DIRECTORY_SEPARATOR;
        if (is_dir($frontend) === true) {
            return $frontend;
        }

        $backend = APPLICATION_PATH . 'themes/backend/' . $theme . DIRECTORY_SEPARATOR;
        if (is_dir($backend) === true) {
            return $backend;
        }
    }

    /**
     * Looks for the requested theme in the frontend and backend theme folder
     * and returns the web path of the theme.
     *
     * @param  string $theme Theme name.
     * @return string Webpath of theme (for usage in templates).
     */
    public function getWebPath($theme = null)
    {
        if ($theme == null) {
            $theme = $this->getName();
        }

        echo APPLICATION_PATH . 'themes/frontend/' . $theme;
        
        // check absolute, return www
        if (is_dir(APPLICATION_PATH . 'themes/frontend/' . $theme) === true) {
             return WWW_ROOT_THEMES_FRONTEND . $theme . '/';
        }

        // check absolute, return www
        if (is_dir(APPLICATION_PATH . 'themes/backend/' . $theme) === true) {
            return WWW_ROOT_THEMES_BACKEND . $theme . '/';
        }
    }

    /**
     * Returns "theme_info.xml" for the requested theme.
     *
     * @param  string                    $theme Theme name.
     * @return string                    File path to "theme_info.xml" file.
     * @throws \Koch\Exception\Exception
     */
    public function getThemeInfoFile($theme)
    {
        $file = $this->getPath($theme) . 'theme_info.xml';

        if (is_file($file) === true) {
            return $file;
        }

        throw new \Exception('The Theme "' . $theme . '" has no "theme_info.xml" file.');
    }

    /**
     * Returns Theme Infos as array.
     *
     * @param  string $theme Name of the Theme.
     * @return array  Theme_Info.xml as Array.
     */
    public function getInfoArray($theme = null)
    {
        $file = $this->getThemeInfoFile($theme);

        $array = \Koch\Config\Adapter\XML::readConfig($file);

        // when setting array as object property remove the inner theme array
        $this->theme_info = $array['theme'];

        return $this->theme_info;
    }

    /**
     * --------------------------------------------------------------------------------------------
     *  GETTERS
     * --------------------------------------------------------------------------------------------
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

    public function getCSSFile()
    {
        $browser = new \Koch\Browser\Browser();

        /* @todo get rid of all this IE stuff */
        $cssPostfix = ($browser->isIE() === true) ? '_ie' : '';

        if (isset($this->theme_info['css']['mainfile']) === true) {
            $part = explode('.', $this->theme_info['css']['mainfile']);
            $cssname = $part[0] . $cssPostfix . '.' . $part[1];

            return $this->getWebPath() . 'css/' . $cssname;
        } elseif (false === isset($this->theme_info['css']['mainfile'])) {
            // maybe we have a theme css file named after the theme
            $css_file = $this->getWebPath() . 'css/' . $this->getName() . '.css';

            if (is_file($css_file)) {
                return $css_file;
            }

            // maybe we have a "import.css" file inside the theme dir
            $css_file = $this->getWebPath() . 'css/import.css';

            if (is_file($css_file)) {
                return $css_file;
            }
        } else {
            // css is hopefully hardcoded or missing !
            return null;
        }
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

    public function getJSFile()
    {
        if (isset($this->theme_info['javascript']['mainfile']) === true) {
            return $this->getWebPath() . 'javascript/' . $this->theme_info['javascript']['mainfile'];
        } elseif (false === isset($this->theme_info['javascript']['mainfile'])) {
            // maybe we have a main javascript file named after the theme
            $js_file = $this->getWebPath() . 'javascript/' . $this->getName() . '.js';

            if (is_file($js_file)) {
                return $js_file;
            }
        } else { // no main javascript file found !

            return null;
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
        $themes = array();

        $themes = array_merge(
            self::iterateDir(APPLICATION_PATH . 'themes/frontend/', 'frontend'),
            self::iterateDir(APPLICATION_PATH . 'themes/backend/', 'backend')
        );

        return $themes;
    }

    /**
     * Iterates over a theme dir (backend / frontend) and fetches some data.
     *
     * @param  string  $dir             APPLICATION_FRONTEND_THEMES_PATH, APPLICATION_BACKEND_THEMES_PATH
     * @param  string  $type            'frontend' or 'backend'
     * @param  boolean $only_index_name
     * @return string
     */
    protected static function iterateDir($dir, $type, $only_index_name = true)
    {
        $dirs = '';
        $dir_tmp = '';
        $i = 0;
        $themes = array();

        $dirs = new \DirectoryIterator($dir);

        foreach ($dirs as $dir) {
            /**
             * Skip early on dots, like "." or ".." or ".svn", by cheching the first char.
             * we can not use DirectoryIterator::isDot() here, because it only checks "." and "..".
             */
            $dir_tmp = $dir->getFilename();

            if ($dir_tmp{0} === '.') {
                continue;
            }

            /**
             * take only directories in account, which contain a "theme_info.xml" file
             */
            if (is_file($dir->getPathName() . DIRECTORY_SEPARATOR . 'theme_info.xml')) {
                $i = $i + 1;

                if ($only_index_name === false) {
                    // add fullpath
                    $themes[$i]['path'] = $dir->getPathName();

                    // set frontend as type
                    $themes[$i]['type']    = $type;

                    // add dirname
                    $themes[$i]['name'] = $type . DIRECTORY_SEPARATOR .  (string) $dir;
                } else {
                    // add dirname
                    $themes[$i] = $type . DIRECTORY_SEPARATOR . (string) $dir;
                }
            }
        }

        unset($i, $dirs, $dir_tmp);

        return $themes;
    }
}
