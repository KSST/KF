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
 */

namespace Koch\View;

use Koch\Router\TargetRoute;
use Koch\Http\HttpRequest;

/**
 * Koch Framework - The View Mapper maps the "action" to the "template".
 *
 * By definition a mapper sets up a communication between two independent objects.
 * View_Mapper is a "class action" to "template" mapper.
 * This has nothing to do with rendering, but with template selection for the view.
 * If no template was set manually in the action of a module (class),
 * this class will help determining the template,
 * by mapping the requested class and action to a template.
 *
 * layout_template selection, depends on the main configuration and user selection (settings).
 */
class Mapper
{
    /**
     * @var string Template name.
     */
    public $template = null;

    /**
     * Returns the Template Name
     * Maps the action name to a template.
     *
     * @return Returns the templateName as String
     */
    public function getTemplateName()
    {
        // if the templateName was not set manually, we construct it from module/action infos
        if (empty($this->template) === true) {
            // construct template name
            $template = TargetRoute::getMethod() . '.tpl';

            $this->setTemplate($template);
        }

        return $this->template;
    }

    /**
     * Set the template name
     *
     * @param string $template Name of the Template with full Path
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get the template name
     *
     * Proxies to \Koch\View\Mapper::getTemplate()
     *
     * @return string $template Name of the Template (full path)
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns the Template Path
     *
     * Fetches the template by searching in the
     * 1) Theme Template Paths
     * 2) Modules Template Paths
     * Note the Path Priority: Themes before Modules.
     *
     * @return $templatepath
     */
    public static function getTemplatePath($template)
    {
        // return early, if template is a qualified path and template filename
        if (is_file($template) === true) {
            return $template;
        }

        // try to find the template in Theme or Module Path
        $theme_template = self::getThemeTemplatePath($template);

        return ($theme_template != null) ? $theme_template : self::getModuleTemplatePath($template);
    }

    /**
     * Return Theme Template Paths
     *
     * @return array Theme Template Paths
     */
    public static function getThemeTemplatePaths()
    {
        // get module, submodule, renderer names
        $module = TargetRoute::getModule();
        $controller = TargetRoute::getController();
        #$renderer  = HttpRequest::getRoute()->getRenderEngine();
        $theme_paths = array();

        /**
         * BACKEND THEME
         * when either "controlcenter" or "admin" is requested, it has to be a BACKEND theme.
         */
        if ($module == 'controlcenter' or $controller == 'admin') {

            $theme = TargetRoute::getBackendTheme();

            // (a) USER BACKENDTHEME - check in the active session backendtheme
            // e.g. /themes/backend/ + admin/template_name.tpl
            $theme_paths[] = APPLICATION_PATH . '/backend/' . $theme;
            // e.g. /themes/backend/ + admin/modules/template_name.tpl
            $theme_paths[] = APPLICATION_PATH . '/backend/' . $theme . '/modules/' . $module . DIRECTORY_SEPARATOR;
            // (b) BACKEND FALLBACK - check the fallback dir: themes/admin
            $theme_paths[] = APPLICATION_PATH . '/backend/default/';
        } else {
            // FRONTEND THEME

            $theme = TargetRoute::getFrontendTheme();

            // (a) USER FRONTENDTHEME - check, if template exists in current session user THEME
            $theme_paths[] = APPLICATION_PATH . '/frontend/' . $theme . DIRECTORY_SEPARATOR;
            // (b) FRONTEND FALLBACK - check, if template exists in usertheme/modulename/tpl
            $theme_paths[] = APPLICATION_PATH . '/frontend/' . $theme . '/modules/' . $module . DIRECTORY_SEPARATOR;
            // (c) FRONTEND FALLBACK - check, if template exists in standard theme
            $theme_paths[] = APPLICATION_PATH . '/frontend/default/';
        }

        return $theme_paths;
    }

    /**
     * Returns the fullpath to Template by searching in the Theme Template Paths
     *
     * Note: For the implementation of module specific renderers and their related templates two ways exist:
     * a) add either a directory named after the "renderer/", like modules/modulename/view/renderer/actioname.tpl
     * b) name fileextension of the templates after the renderer (.xtpl, .phptpl, .tal).
     *
     * @param  string $template Template Filename
     * @return string
     */
    public static function getThemeTemplatePath($template)
    {
        $paths = self::getThemeTemplatePaths();

        return self::findFileInPaths($paths, $template);
    }

    /**
     * Returns Module Template Paths
     *
     * @return array Module Template Paths
     */
    public static function getModuleTemplatePaths($module = null)
    {
        // fetch modulename for template path construction
        if (empty($module) === true) {
            $module = TargetRoute::getModule();
        }

        // fetch renderer name for template path construction
        $renderer = TargetRoute::getRenderEngine();

        // compose templates paths in the module dir
        $module_paths = array(
            APPLICATION_MODULES_PATH,
            APPLICATION_MODULES_PATH . $module . DIRECTORY_SEPARATOR,
            APPLICATION_MODULES_PATH . $module . '/View/',
            APPLICATION_MODULES_PATH . $module . '/View/' . ucfirst($renderer) . DIRECTORY_SEPARATOR
        );

        return $module_paths;
    }

    /**
     * Returns the fullpath to Template by searching in the Module Template Path
     *
     * @param  string $template Template Filename
     * @return string
     */
    public static function getModuleTemplatePath($template, $module = null)
    {
        $paths = self::getModuleTemplatePaths($module);

        // check if template exists in one of the defined paths
        $module_template = self::findFileInPaths($paths, $template);

        #\Koch\Debug\Debug::firebug('Module Template: ' . $module_template . '<br />');

        if ($module_template != null) {
            return $module_template;
        } else {
            // fetch renderer name for template path construction
            $renderer = HttpRequest::getRoute()->getRenderEngine();

            // the template with that name is not found on our default paths
            // @todo if this would be a html template, we could skip determining the render engine
            return APPLICATION_PATH . 'themes/core/view/' . $renderer . '/template_not_found.tpl';
        }
    }

    /**
     * Checks all paths of the array for the filename
     *
     * @param  array  $paths    Paths to check
     * @param  strig  $filename template name
     * @return string Filepath.
     */
    public static function findFileInPaths($paths, $filename)
    {
        // check if the file exists in one of the defined paths
        foreach ($paths as $path) {
            $file = $path . $filename;
            #\Koch\Debug\Debug::dump($file, false);
            if (is_file($file) === true) {
                // file found
                return $file;
            }
        }

        // file not found
        return false;
    }
}
