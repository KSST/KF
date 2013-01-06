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

namespace Koch\View;

use Koch\Http\HttpRequest;

/**
 * A abstract base class for all our view rendering engines.
 * All renderers must extend from this class.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Renderer
 */
abstract class AbstractRenderer
{
    protected $options = array(
        'a' => '',
        'b' => ''
    );

    /**
     * @var Object Holds instance of the Rendering Engine Object
     */
    public $renderer = null;

    /**
     * @var object Holds instance of the Theme Object
     */
    public $theme = null;

    /**
     * @var string The layout template
     */
    public $layoutTemplate = null;

    /**
     * @var string Variable for the RenderMode (LAYOUT, NOLAYOUT)
     */
    public $renderMode = null;

    /**
     * @var object Koch\Config
     */
    protected $config = null;

    /**
     * @var array|object Viewdata
     */
    public $viewdata = array();

    /**
     * @var object Koch_View_Mapper
     */
    public $viewMapper = null;

    /**
     * Directive for auto-escaping of template variables before rendering.
     * @todo
     * @var boolean
     */
    #public $autoEscape = true;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }

        $this->viewMapper = new \Koch\View\Mapper;
    }

    /**
     * Returns the render engine object
     *
     * @return Renderer
     */
    public function getEngine()
    {
        if($this->renderer !== null) {
            return $this->renderer;
        }
    }

    /**
     * Initialize the render engine object
     *
     * @param  string $template Template Name for "Frontloader" Rendering Engines (xtpl).
     * @return Engine Object
     */
    abstract public function initializeEngine($template = null);

    /**
     * Configure the render engine object
     */
    abstract public function configureEngine();

    /**
     * Renders the given Template with renderMode wrapped (with Layout)
     *
     * @param string Template
     * @param array|object Data to assign to the template.
     * @return string
     */
    abstract public function render($template, $viewdata = null);

    /**
     * Assigns a value to a template parameter.
     *
     * @param string $tpl_parameter The template parameter name
     * @param mixed  $value         The value to assign
     */
    abstract public function assign($tpl_parameter, $value = null);

    /**
     * Executes the template rendering and returns the result.
     *
     * @param  string $template Template Filename
     * @param  mixed  $data     Additional data to process
     * @return string
     */
    abstract public function fetch($template, $viewdata = null);

    /**
     * Executes the template rendering and displays the result.
     *
     * @param  string $template Template Filename
     * @param  mixed  $data     Additional data to process
     * @return string
     */
    abstract public function display($template, $viewdata = null);

    /**
     * Clear all assigned Variables
     */
    public function clearVars()
    {
        $this->viewdata = null;
    }

    /**
     * Returns all assigned template variables.
     */
    public function getVars()
    {
        return $this->viewdata;
    }

    public function getViewMapper()
    {
        return $this->viewMapper;
    }

    public function setViewMapper(\Koch\View\Mapper $viewMapper)
    {
        $this->viewMapper = $viewMapper;
    }

    /**
     * Set the template name.
     *
     * Proxies to Koch_View_Mapper::setTemplate()
     *
     * @param string $template Name of the Template (full path)
     */
    public function setTemplate($template)
    {
        $this->getViewMapper()->setTemplate($template);
    }

    /**
     * Get the template name
     *
     * Proxies to Koch_View_Mapper::getTemplate()
     *
     * @return string $template Name of the Template (full path)
     */
    public function getTemplate()
    {
        return $this->getViewMapper()->getTemplate();
    }

    /**
     * Constants for overall usage in all templates of all render engines
     *
     * a) Assign Web Paths
     * b) Meta
     * c) Application version
     * d) Page related
     *
     * @return array $template_constants
     */
    public function getConstants()
    {
        $modulename = HttpRequest::getRoute()->getModule();

        $templateConstants = array();

        /**
         * a) Assign Web Paths
         *
         *    Watch it! These Paths are relative (based on WWW_ROOT), not absolute!
         */
        $templateConstants['www_root']                 = WWW_ROOT;
        $templateConstants['www_root_uploads']         = WWW_ROOT . 'Uploads/';
        $templateConstants['www_root_modules']         = WWW_ROOT . 'Modules/' . $modulename . '/';
        $templateConstants['www_root_theme']           = $this->getTheme()->getWebPath();
        $templateConstants['www_root_themes']          = WWW_ROOT_THEMES;
        $templateConstants['www_root_themes_core']     = WWW_ROOT_THEMES_CORE;
        $templateConstants['www_root_themes_backend']  = WWW_ROOT_THEMES_BACKEND;
        $templateConstants['www_root_themes_frontend'] = WWW_ROOT_THEMES_FRONTEND;

        /**
         * b) Meta Informations
         */
        $templateConstants['meta'] = $this->config['meta'];

        /**
         * c) Application Version
         *
         *    Note: This is doubled functionality.
         *    You can use $smarty.const.APPLICATION_VERSION or $application_version in a template.
         */
        $templateConstants['application_version']       = APPLICATION_VERSION;
        $templateConstants['application_version_state'] = APPLICATION_VERSION_STATE;
        $templateConstants['application_version_name']  = APPLICATION_VERSION_NAME;
        $templateConstants['application_url']           = APPLICATION_URL;

        /**
         * d) Page related
         */

        // Page Title
        $templateConstants['pagetitle'] = $this->config['template']['pagetitle'];

        // Normal CSS (mainfile)
        $templateConstants['css'] = $this->getTheme()->getCSSFile();

        // Normal Javascript (mainfile)
        $templateConstants['javascript'] = $this->getTheme()->getJSFile();

        // Breadcrumb
        $templateConstants['trail'] = \Koch\View\Helper\Breadcrumb::getTrail();

        // Templatename itself
        $templateConstants['templatename'] = $this->getTemplate();

        // Help Tracking
        $templateConstants['helptracking'] = $this->config['help']['tracking'];

        /**
         * Debug Display
         */
        #\Koch\Debug\Debug::printR($templateConstants);

        return $templateConstants;
    }

    /**
     * Set the Layout Template. The layout template is a Wrapper-Template.
     * The Content of a Module is rendered at variable {$content} inside this layout!
     *
     * @param string $template Template Filename for the wrapper Layout
     */
    public function setLayoutTemplate($template)
    {
        $this->layoutTemplate = $template;
    }

    /**
     * Returns the Name of the Layout Template.
     * Returns the config value if no layout template is set.
     *
     * @return string layout name, config layout as default
     */
    public function getLayoutTemplate()
    {
        if ($this->layoutTemplate == null) {
            $this->setLayoutTemplate($this->getTheme()->getLayoutFile());
        }

        return $this->layoutTemplate;
    }

    /**
     * Returns the object Koch_Theme for accessing Configuration Values
     *
     * @return object Koch_Theme
     */
    public function getTheme()
    {
        if ($this->theme === null) {
            $theme = HttpRequest::getRoute()->getThemeName();

            $this->theme = new \Koch\View\Helper\Theme($theme);
        }

        return $this->theme;
    }

    /**
     * Auto-Escape for Template Variables.
     * This reduces the risk of forgetting to escape vars correctly.
     *
     * All variables assign to the template will be STRINGS,
     * because htmlentities will cast all values to string.
     * Character encoding used is UTF-8.
     *
     * @todo: do we need a config toggle for this?
     *
     * @param  string  $key The variable name.
     * @param  mixed   $val The variable value.
     * @return boolean True if data was assigned to view; false if not.
     */
    public function autoEscape($key, $value)
    {
        if (is_array($value)) {
            $clean = array();
            foreach ($value as $key2 => $value2) {
                $clean[$key2] = htmlentities($value2, ENT_QUOTES, 'utf-8');
            }

            return $this->assign($clean);
        } else {
            $clean = htmlentities($value2, ENT_QUOTES, 'utf-8');

            return $this->assign($key, $clean);
        }
    }

    /**
     * Magic Method __call / Overloading.
     *
     * This is basically a simple passthrough (aggregation)
     * of a method and its arguments to the rendering engine!
     * Purpose: We don't have to rebuild all methods in the specific render engine adapter
     * or pull out the render engine object itself. We just pass things to it.
     *
     * @param string $method    Name of the Method
     * @param array  $arguments Array with Arguments
     *
     * @return Function Call to Method
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->renderer, $method) === true) {
            return call_user_func_array(array($this->renderer, $method), $arguments);
        } else {
            throw new \InvalidArgumentException(
                'Method "'. $method .'()" not existant in Render Engine "' . get_class($this->renderer) .'"!'
            );
        }
    }

    // object duplication / cloning is not permitted
    protected function __clone()
    {
        return;
    }

    /**
     * Set renderer variables
     *
     * @param string $key   variable name
     * @param string $value variable value
     */
    public function __set($key, $value)
    {
        $this->renderer->assign($key, $value);
    }

    /**
     * Get renderer Variable Value
     *
     * @param  string $key variable name
     * @return mixed  variable value
     */
    public function __get($key)
    {
        return $this->renderer->$key;
    }

    /**
     * Check if renderer variable is set
     *
     * @param string $key variable name
     */
    public function __isset($key)
    {
        return isset($this->renderer->$key);
    }

    /**
     * Unset renderer variable
     *
     * @param string $key variable name
     */
    public function __unset($key)
    {
        if ($this->renderer->$key !== null) {
            unset($this->renderer->$key);
        }
    }
}
