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

namespace Koch\View;

use Exception;

/**
 * Abstract base class for View Renderers.
 *
 * A abstract base class for all our view rendering engines.
 * All renderers must extend from this class.
 */
abstract class AbstractRenderer
{
    /**
     * Options Array.
     *
     * @var array
     */
    public $options = [];

    /**
     * @var object Holds instance of the Rendering Engine Object
     */
    public $renderer;

    /**
     * @var object Holds instance of the Theme Object
     */
    public $theme;

    /**
     * @var string The layout template
     */
    public $layoutTemplate;

    /**
     * @var string Variable for the RenderMode (LAYOUT, PARTIAL)
     */
    public $renderMode;

    /**
     * @var object Koch\Config
     */
    protected $config;

    /**
     * @var array|object Viewdata
     */
    public $viewdata = [];

    /**
     * @var object Koch\View\Mapper
     */
    public $viewMapper;

    /**
     * Directive for auto-escaping of template variables before rendering.
     *
     * @todo
     *
     * @var bool
     */
    #public $autoEscape = true;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);

        $this->viewMapper = new \Koch\View\Mapper();
    }

    /**
     * Returns options array.
     *
     * @return array Options.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options.
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    /**
     * Returns the render engine object.
     *
     * @return Renderer
     */
    public function getEngine()
    {
        if ($this->renderer !== null) {
            return $this->renderer;
        }
    }

    /**
     * Initialize the render engine object.
     *
     * @param string $template Template Name for "Frontloader" Rendering Engines (xtpl).
     *
     * @return Engine Object
     */
    abstract public function initializeEngine($template = null);

    /**
     * Configure the render engine object.
     */
    abstract public function configureEngine();

    /**
     * Renders the given Template with renderMode wrapped (with Layout).
     *
     * @param string Template
     * @param array|object Data to assign to the template.
     *
     * @return string
     */
    abstract public function render($template = null, $viewdata = null);

    /**
     * Assigns a value to a template parameter.
     *
     * @param string $tpl_parameter The template parameter name
     */
    abstract public function assign($tpl_parameter, mixed $value = null);

    /**
     * Executes the template rendering and returns the result.
     *
     * @param string $template Template Filename
     *
     * @return string
     */
    abstract public function fetch($template, $viewdata = null);

    /**
     * Executes the template rendering and displays the result.
     *
     * @param string $template Template Filename
     *
     * @return void
     */
    abstract public function display($template, mixed $viewdata = null);

    /**
     * Clear all assigned Variables.
     */
    public function clearVars()
    {
        return $this->viewdata = null;
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
     * Proxies to Koch\View\Mapper::setTemplate()
     *
     * @param string $template Name of the Template (full path)
     */
    public function setTemplate($template)
    {
        $this->getViewMapper()->setTemplate($template);
    }

    /**
     * Get the template name.
     *
     * Proxies to Koch\View\Mapper::getTemplate()
     *
     * @return string $template Name of the Template (full path)
     */
    public function getTemplate()
    {
        return $this->getViewMapper()->getTemplate();
    }

    /**
     * Constants for overall usage in all templates of all render engines.
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
        $modulename = \Koch\Router\TargetRoute::getModule();

        $templateConstants = [];

        /*
         * a) Assign Web Paths
         *
         *    Watch it! These are relative (not absolute) paths. They are based on WWW_ROOT!
         */
        $templateConstants['www_root']                 = WWW_ROOT;
        $templateConstants['www_root_uploads']         = WWW_ROOT . 'Uploads/';
        $templateConstants['www_root_modules']         = WWW_ROOT . 'Modules/' . $modulename . '/';
        $templateConstants['www_root_theme']           = $this->getTheme()->getWebPath();
        $templateConstants['www_root_themes']          = WWW_ROOT_THEMES;
        $templateConstants['www_root_themes_core']     = WWW_ROOT_THEMES_CORE;
        $templateConstants['www_root_themes_backend']  = WWW_ROOT_THEMES_BACKEND;
        $templateConstants['www_root_themes_frontend'] = WWW_ROOT_THEMES_FRONTEND;

        /*
         * b) Meta Informations
         */
        $templateConstants['meta'] = $this->config['meta'];

        /*
         * c) Application Version
         *
         *    Note: This is doubled functionality.
         *    You can use $smarty.const.APPLICATION_VERSION or $application_version in a template.
         */
        $templateConstants['application_version']       = APPLICATION_VERSION;
        $templateConstants['application_version_state'] = APPLICATION_VERSION_STATE;
        $templateConstants['application_version_name']  = APPLICATION_VERSION_NAME;
        $templateConstants['application_url']           = APPLICATION_URL;

        /*
         * d) Page related
         */

        // Page Title
        $templateConstants['pagetitle'] = $this->config['template']['pagetitle'];

        // Breadcrumb
        $templateConstants['trail'] = \Koch\View\Helper\Breadcrumb::getTrail();

        // Templatename itself
        $templateConstants['templatename'] = $this->getTemplate();

        // Help Tracking
        $templateConstants['helptracking'] = $this->config['help']['tracking'];

        /*
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
        if ($this->layoutTemplate === null) {
            $this->setLayoutTemplate($this->getTheme()->getLayoutFile());
        }

        return $this->layoutTemplate;
    }

    /**
     * Returns the object Koch_Theme for accessing Configuration Values.
     *
     * @return object Koch_Theme
     */
    public function getTheme()
    {
        if ($this->theme === null) {
            $theme = \Koch\Router\TargetRoute::getThemeName();

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
     * @param string $key   The variable name.
     *
     * @return bool True if data was assigned to view; false if not.
     */
    public function autoEscape($key, mixed $value)
    {
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key2 => $value2) {
                $clean[$key2] = htmlentities((string) $value2, ENT_QUOTES, 'utf-8');
            }
        } else {
            $clean = htmlentities((string) $value, ENT_QUOTES, 'utf-8');
        }

        return $this->assign($key, $clean);
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
        if (method_exists($this->renderer, $method)) {
            return call_user_func_array([$this->renderer, $method], $arguments);
        } else {
            throw new \InvalidArgumentException(
                'Method "' . $method . '()" not existant in Render Engine "' . $this->renderer::class . '"!'
            );
        }
    }

    /**
     * Cloning instances of the class is forbidden
     */
    private function __clone()
    {
        throw new Exception("Object cloning is not permitted.");
    }

    /**
     * Set renderer variables.
     *
     * @param string $key   variable name
     * @param string $value variable value
     */
    public function __set($key, $value)
    {
        $this->renderer->assign($key, $value);
    }

    /**
     * Get renderer Variable Value.
     *
     * @param string $key variable name
     *
     * @return mixed variable value
     */
    public function __get($key)
    {
        return $this->renderer->$key;
    }

    /**
     * Check if renderer variable is set.
     *
     * @param string $key variable name
     */
    public function __isset($key)
    {
        return isset($this->renderer->$key);
    }

    /**
     * Unset renderer variable.
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
