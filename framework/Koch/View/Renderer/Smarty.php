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

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;
use Koch\View\Mapper;
use Koch\Router\TargetRoute;

/**
 * Koch Framework - View Renderer for Smarty Templates.
 *
 * This is a wrapper/adapter for the Smarty Template Engine.
 *
 * @link http://smarty.php.net/ Official Website of Smarty Template Engine
 * @link http://smarty.incutio.com/ Smarty Wiki
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Smarty extends AbstractRenderer
{
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        // fallback config
        $this->options = array(
           'compile_dir' => APPLICATION_CACHE_PATH . 'tpl_compile/',
           'cache_dir' => APPLICATION_CACHE_PATH . 'tpl_cache/',
           'config_dir' => '',
           'debug_tpl' => '', #APPLICATION_THEMES_CORE . 'view/smarty/debug.tpl',
           'cache_enabled' => 0,
           'cache_lifetime' => 0
        );

        // set incoming config
        parent::__construct($options);

        $this->initializeEngine();

        $this->configureEngine();

        // debug display of all smarty related directories
        //$this->renderer->testInstall();
    }

    /**
     * Set up Smarty Template Engine
     *
     * @param string $template Template Name for "Frontloader" Rendering Engines (xtpl).
     */
    public function initializeEngine($template = null)
    {
        // Do it with smarty style > eat like a bird, poop like an elefant!
        /* @var Smarty */
        $this->renderer = new \Smarty();
    }

    /**
     * Render Engine Configuration
     * Configures the Smarty Object
     */
    public function configureEngine()
    {
        // Directories
        $this->renderer->compile_dir = $this->options['compile_dir'];
        $this->renderer->cache_dir   = $this->options['cache_dir'];
        $this->renderer->config_dir  = $this->options['config_dir'];

        /**
         * Debug Mode Settings
         */
        if (DEBUG == true) {

            $this->renderer->debugging = true;

            $this->renderer->debug_tpl = 'file:' . $this->options['debug_tpl'];

            $this->renderer->caching = 0;
            $this->renderer->cache_lifetime = 0;             // refresh templates on every load
            // $this->renderer->cache_handler_func   = "";   // Specify your own cache_handler function
            $this->renderer->cache_modified_check = 0;       // set to 1 to activate

            $this->renderer->clearCompiledTemplate();
            $this->renderer->clearAllCache();

            /**
             * Recompile templates only in debug mode
             * @see http://www.smarty.net/manual/de/variable.compile.check.php
             */
            // if a template was changed it would be recompiled,
            // if set to false nothing will be compiled (changes take no effect)
            $this->renderer->compile_check = true;
            // if true compiles each template everytime, overwrites $compile_check
            $this->renderer->force_compile = true;
        }

        // auto delimiter of javascript/css
        $this->renderer->auto_literal = true;

        /**
         * Caching
         */
        $this->renderer->caching = (bool) $this->options['cache_enabled'];
        // -1 ... dont expire, 0 ... refresh everytime
        $this->renderer->cache_lifetime = $this->options['cache_lifetime'];
        // $this->renderer->cache_handler_func = "";      // Specify your own cache_handler function
        $this->renderer->cache_modified_check = 1;       // set to 1 to activate

        /**
         * Smarty Template Directories
         *
         * This sets multiple template dirs, with the following detection order:
         * The user-choosen Theme, before Module, before Core/Default/Admin-Theme.
         *
         * 1) "/themes/[frontend|backend]/theme(from_session)/"
         * 2) "/themes/[frontend|backend]/theme(from_session)/modulename/"
         * 3) "/modules/"
         * 4) "/modules/modulename/view/"
         * 5) "/themes/core/view/smarty"
         * 6) "/themes/"
         */
        $tpl_array = array(
            $this->viewMapper->getThemeTemplatePaths(), // 1 + 2
            $this->viewMapper->getModuleTemplatePaths(), // 3 + 4
            APPLICATION_PATH . 'themes/core/view/smarty', // 5
            APPLICATION_PATH . 'themes/' // 6
        );

        // flatten that thing
        $this->renderer->template_dir = \Koch\Functions\Functions::array_flatten($tpl_array);

        #\Koch\Debug\Debug::printR($this->renderer->template_dir);

        /**
         * Smarty Plugins
         *
         * Configure Smarty Viewhelper Directories
         * 1) original smarty plugins           => vendor folder \smarty\plugins\
         * 2) framework                         => framework folder \view\helper\smarty
         * 3) application core smarty plugins   => application folder \core\view\helper\smarty\
         * 4) application module smarty plugins => application module \app\modules\(modulename)\view\helper\smarty\
         */

        $this->renderer->setPluginsDir(
            array(
                VENDOR_PATH . '/smarty/smarty/distribution/libs/plugins',
                __DIR__ . '/../Helper/Smarty',
                APPLICATION_PATH . '/Core/View/Helper/Smarty',
                APPLICATION_MODULES_PATH . TargetRoute::getModule() . '/View/Helper/Smarty'
            )
        );

        #\Koch\Debug\Debug::printR($this->renderer->plugins_dir);

        // $this->renderer->registerPlugin('modifier', 'timemarker',  array('benchmark', 'timemarker'));

        /**
         * SMARTY FILTERS
         */
        $autoload_filters = array();
        if ($this->renderer->debugging === true) {
            $autoload_filters = array('pre' => array('inserttplnames'));
        }
        $this->renderer->autoload_filters = $autoload_filters;
        #array(       // indicates which filters will be auto-loaded
        #'pre'    => array('inserttplnames'),
        #'post'   => array(),
        #'output' => array('trimwhitespaces')
        #);
        //
        #$this->renderer->registerFilter(Smarty::FILTER_VARIABLE, 'htmlspecialchars');

        // Auto-Escape all variables
        #$this->renderer->default_modifiers = array('escape:"html":"UTF-8"');

        // compile time setting, tpls need recompiling
        $this->renderer->merge_compiled_includes = true;
    }

    /**
     * Returns a clean Smarty Object
     *
     * @return Smarty Object
     */
    public function getEngine()
    {
        if (is_object($this->renderer) === true) {
            // reset all prior assigns and configuration settings
            $this->renderer->clearAllAssign();
            $this->renderer->clearConfig();
        } else {
            self::initializeEngine();
        }

        // reload the base configuration, to have default template paths and debug-settings
        self::configureEngine();

        return $this->renderer;
    }

    /**
     * Adds a Template Path
     *
     * @param string $templatepath Template-Directory to have Smarty search in
     */
    public function setTemplatePath($templatepath)
    {
        if (is_dir($templatepath) === true and is_readable($templatepath) === true) {
            $this->renderer->setTemplateDir($templatepath);
        } else {
            throw new \InvalidArgumentException(
                'Invalid Smarty Template path provided: Path not existing or not readable. Path: ' . $templatepath
            );
        }
    }

    /**
     * Get all template paths from Smarty
     *
     * @return array
     */
    public function getTemplatePaths()
    {
        return $this->renderer->template_dir;
    }

    /**
     * Set/Assign a Variable to Smarty
     *
     * 1. Set a single Key-Variable ($tpl_parameter) with it's value ($value)
     * 2. Set a array with multiple Keys and Values
     *
     * @see __set()
     * @param  string|array $tpl_parameter Is a Key or an Array.
     * @param  mixed        $value         (optional) In case a key-value pair is used, $value is the value.
     * @return boolean
     */
    public function assign($tpl_parameter, $value = null)
    {
        if (is_array($tpl_parameter) === true or is_object($tpl_parameter) === true ) {
            return $this->renderer->assign($tpl_parameter);
        } else {
            return $this->renderer->assign($tpl_parameter, $value);
        }
    }

    /**
     * Magic Method to get a already set/assigned Variable from Smarty
     *
     * @param  string $key Name of Variable
     * @return mixed  Value of key
     */
    public function __get($key)
    {
        return $this->renderer->getTemplateVars($key);
    }

    /**
     * Magic Method to set/assign Variable to Smarty
     *
     * @param  string  $key Name of the variable
     * @param  mixed   $val Value of variable
     * @return boolean
     */
    public function __set($key, $value)
    {
        return $this->assign($key, $value);
    }

     /**
     * Magic Method to testing with empty() and isset() for Smarty Template Variables
     *
     * @param  string  $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->renderer->getTemplateVars($key));
    }

    /**
     * Magic Method to unset() Smarty Template Variables
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->renderer->clearAssign($key);
    }

    /**
     * Executes the template fetching and returns the result.
     *
     * @param  string|object $template   the resource handle of the template file  or template object
     * @param  mixed         $cache_id   cache id to be used with this template
     * @param  mixed         $compile_id compile id to be used with this template
     * @param  object        $parent     next higher level of Smarty variables
     * @param  boolean       $display    Renders the template content on true.
     * @return Returns       the $template content.
     */
    public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
    {
        // ask the view mapper for the path of the template
        $template = Mapper::getTemplatePath($template);

        // create cache_id
        if ($cache_id === null) {
            $cache_id = $this->createCacheId();
        }

        return $this->renderer->fetch($template, $cache_id, $compile_id, $parent, $display);
    }

    /**
     * Creates a cache_id.
     *
     * @return string Returns md5 string as cache_id.
     */
    public static function createCacheId()
    {
        $module = TargetRoute::getModule();
        $controller = TargetRoute::getController();
        $action = TargetRoute::getMethod();

        return md5(strtolower($module . $controller . $action));
    }

    /**
     * Executes the template rendering and displays the result.
     *
     * @param string|object $template   the resource handle of the template file  or template object
     * @param mixed         $cache_id   cache id to be used with this template
     * @param mixed         $compile_id compile id to be used with this template
     * @param object        $parent     next higher level of Smarty variables
     */
    public function display($template, $cache_id = null, $compile_id = null, $parent = null)
    {
        // redirect to fetch, but set display to true
        return $this->fetch($template, $cache_id, $compile_id, $parent, true);
    }

    /**
     * Returns all assigned template variables.
     *
     * @return array
     */
    public function getVars()
    {
        return $this->renderer->getTemplateVars();
    }

    /**
     * Clears all assigned Variables
     */
    public function clearVars()
    {
        $this->renderer->clearAllAssign();
    }

    /**
     * Clears the Smarty Template Cache folder and removes compiled Templates
     */
    public function resetCache()
    {
        // empty cache folder
        $b1 = $this->renderer->clearAllCache();
        // empty compile folder
        $b2 = $this->renderer->clearCompiledTemplate();

        //return ($b1 && $b2);
        return true;
    }

    /**
     * Setter for RenderMode
     *
     * @param string $mode Set the renderMode (LAYOUT, NOLAYOUT). Default LAYOUT.
     */
    public function setRenderMode($mode = 'LAYOUT')
    {
        $mode = strtoupper($mode);

        if ($mode === 'LAYOUT' or $mode === 'NOLAYOUT') {
            $this->renderMode = $mode;
        } else {
            throw new \InvalidArgumentException('Use LAYOUT or NOLAYOUT as parameter.');
        }
    }

    /**
     * Getter for RenderMode
     *
     * @return string Returns the renderMode (LAYOUT, NOLAYOUT). Defaults to LAYOUT.
     */
    public function getRenderMode()
    {
        if ($this->renderMode === null) {
            $this->renderMode = 'LAYOUT';
        }

        return $this->renderMode;
    }

    /**
     * Renderer_Smarty->render
     *
     * Returns the mainframe layout with inserted modulcontent (templatename).
     *
     * 1. assign common values and constants
     * 2. fetch the modultemplate and assigns it as $content
     * 3. return the wrapper layout tpl
     *
     * @param  string       $templatename Template Filename
     * @param  array|object $data         Data to assign to the view.
     * @return wrapper      tpl layout
     */
    public function render($template, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->assign($viewdata);
        }

        // assign common template values and Application constants as Smarty Template Variables.
        $constants = $this->getConstants();
        foreach ($constants as $const => $value) {
            $this->renderer->assignGlobal($const, $value);
        }

        /**
         * Assign the original template name and the requested module
         * This is used in template_not_found.tpl to provide a link to the templateeditor
         */
        $this->renderer->assignGlobal('modulename', TargetRoute::getModule());
        $this->renderer->assignGlobal('actionname', TargetRoute::getActionName());
        $this->renderer->assignGlobal('templatename', $template);

        /**
         * Rendering depends on the RenderMode.
         *
         * RenderMode "NOLAYOUT" means that only the (module) content template is rendered.
         *
         * RenderMode "LAYOUT" means that the (module) content template is embedded,
         * into a layout template, by replacing the {$content} placeholder.
         */
        if ($this->getRenderMode() === 'NOLAYOUT') {
            return $this->fetch($template);
        }

        if ($this->getRenderMode() === 'LAYOUT') {
                // assign the modulecontent
                $this->assign('content', $this->fetch($template));

                return $this->fetch($this->getLayoutTemplate());
        }
    }
}
