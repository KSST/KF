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
     * @var string Variable for the RenderMode (available: WRAPPED)
     */
    public $renderMode = null;

    /**
     * @var object Koch\Config
     */
    protected $config = null;

    /**
     * @var array|object Viewdata
     */
    public $viewdata = null;

    /**
     * @var object Koch_View_Mapper
     */
    public $view_mapper = null;

    /**
     * Directive for auto-escaping of template variables before rendering.
     * @todo
     * @var boolean
     */
    #public $autoEscape = true;

    /**
     * Construct Renderer
     *
     * @param Koch\Config Object
     */
    public function __construct(\Koch\Config\Config $config)
    {
        $this->config = $config;
        $this->view_mapper = new \Koch\View\Mapper();
    }

    /**
     * Returns the render engine object
     *
     * @return string
     */
    abstract public function getEngine();

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
    abstract public function render($template, $viewdata);

    /**
     * Renders the given Template with renderMode unwrapped (without Layout)
     *
     * @return string
     */
    abstract public function renderPartial($template);

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
    abstract public function fetch($template, $data = null);

    /**
     * Executes the template rendering and displays the result.
     *
     * @param  string $template Template Filename
     * @param  mixed  $data     Additional data to process
     * @return string
     */
    abstract public function display($template, $data = null);

    /**
     * Clear all assigned Variables
     */
    abstract public function clearVars();

    /**
     * Returns all assigned template variables.
     */
    abstract public function getVars();

    /**
     * Enables / disables the caching of templates.
     */
    abstract public function setCaching($boolean);

    /**
     * Checks if a template is cached.
     *
     * @param  string  $template   the resource handle of the template file or template object
     * @param  mixed   $cache_id   cache id to be used with this template
     * @param  mixed   $compile_id compile id to be used with this template
     * @return boolean Returns true in case the template is cached, false otherwise.
     */
    abstract public function isCached($template, $cache_id = null, $compile_id = null);

    /**
     * Reset the Cache of the Renderer
     */
    abstract public function clearCache(
        $template_name,
        $cache_id = null,
        $compile_id = null,
        $exp_time = null,
        $type = null
    );

    public function getViewMapper()
    {
        return $this->view_mapper;
    }

    public function setViewMapper(\Koch\View\Mapper $view_mapper)
    {
        $this->view_mapper = $view_mapper;
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

        $template_constants = array();

        /**
         * a) Assign Web Paths
         *
         *    Watch it! These Paths are relative (based on WWW_ROOT), not absolute!
         */
        $template_constants['www_root']                 = WWW_ROOT;
        $template_constants['www_root_uploads']         = WWW_ROOT . 'Uploads/';
        $template_constants['www_root_modules']         = WWW_ROOT . 'Modules/' . $modulename . '/';
        $template_constants['www_root_theme']           = $this->getTheme()->getWebPath();
        $template_constants['www_root_themes']          = WWW_ROOT_THEMES;
        $template_constants['www_root_themes_core']     = WWW_ROOT_THEMES_CORE;
        $template_constants['www_root_themes_backend']  = WWW_ROOT_THEMES_BACKEND;
        $template_constants['www_root_themes_frontend'] = WWW_ROOT_THEMES_FRONTEND;

        /**
         * b) Meta Informations
         */
        $template_constants['meta'] = $this->config['meta'];

        /**
         * c) Application Version
         *
         *    Note: This is doubled functionality.
         *    You can use $smarty.const.APPLICATION_VERSION or $application_version in a template.
         */
        $template_constants['application_version']       = APPLICATION_VERSION;
        $template_constants['application_version_state'] = APPLICATION_VERSION_STATE;
        $template_constants['application_version_name']  = APPLICATION_VERSION_NAME;
        $template_constants['application_url']           = APPLICATION_URL;

        /**
         * d) Page related
         */

        // Page Title
        $template_constants['pagetitle'] = $this->config['template']['pagetitle'];

        // Normal CSS (mainfile)
        $template_constants['css'] = $this->getTheme()->getCSSFile();

        // Normal Javascript (mainfile)
        $template_constants['javascript'] = $this->getTheme()->getJSFile();

        // Breadcrumb
        $template_constants['trail'] = \Koch\View\Helper\Breadcrumb::getTrail();

        // Templatename itself
        $template_constants['templatename'] = $this->getTemplate();

        // Help Tracking
        $template_constants['helptracking'] = $this->config['help']['tracking'];

        /**
         * Debug Display
         */
        #\Koch\Debug\Debug::printR($template_constants);

        return $template_constants;
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
            $themename = HttpRequest::getRoute()->getThemeName();

            $this->theme = new \Koch\View\Helper\Theme($themename);
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
     * of a method and its arguments to the renderingEngine!
     * Purpose: We don't have to rebuild all methods in the specific renderEngine Wrapper/Adapter
     * or pull out the renderEngine Object itself. We just pass things to it.
     *
     * @param string $method    Name of the Method
     * @param array  $arguments Array with Arguments
     *
     * @return Function Call to Method
     */
    public function __call($method, $arguments)
    {
        #echo'Magic used for Loading Method = '. $method . ' with Arguments = '. var_dump($arguments);
        if (method_exists($this->renderer, $method)) {
            return call_user_func_array(array($this->renderer, $method), $arguments);
        } else {
            throw new Exception(
                'Method "'. $method .'()" not existant in Render Engine "' . get_class($this->renderer) .'"!'
            );
        }
    }

    // object duplication / cloning is not permitted
    protected function __clone()
    {
        return;
    }
}
