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

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * View Renderer for PHPTAL templates.
 *
 * This is a wrapper/adapter for rendering with PHPTAL.
 *
 * @link http://phptal.org/ Official Website of the PHPTal Project.
 * @link http://phptal.sourceforge.net/ Project's Website at Sourceforge
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Phptal extends AbstractRenderer
{
    /**
     * RenderEngineConstructor
     *
     * parent::__construct does the following:
     * 1) Apply instances of Dependency Injector Phemto and Koch\Config to the RenderBase
     * 2) Initialize the RenderEngine via parent class constructor call = self::initializeEngine()
     * 3) Configure the RenderEngine with it's specific settings = self::configureEngine();
     * 4) Eventlog
     */
    public function __construct(\Koch\Config\Config $config)
    {
        parent::__construct($config);
        $this->initializeEngine();
        $this->configureEngine();
    }

    /**
     * Sets up PHPTAL Template Engine
     *
     * @return PHPTAL Object
     */
    public function initializeEngine($template = null)
    {
        if (class_exists('PHPTAL') === false) {
            throw new \Koch\Exception\Exception('The vendor library "PHPTal" is missing!');
        }

        // Do it with phptal style > eat like a bird, poop like an elefant!
        $this->renderer = new \PHPTAL($template);
    }

    /**
     * Add data to the PHPTAL view
     *
     * @param mixed $tpl_parameter The placeholder.
     * @param mixed $value         The value.
     */
    public function assign($tpl_parameter, $value = null)
    {
        if (is_array($tpl_parameter)) {
            foreach ($tpl_parameter as $param => $val) {
                $this->renderer->$param = $val;
            }
        } else {
            if ($tpl_parameter != null) {
                $this->renderer->$tpl_parameter = $value;
            }
        }
    }

    public function setTemplate($template)
    {
        $this->renderer->setTemplate($template);
    }

    /**
     * Render Engine Configuration
     * Configures the PHPTAL Object
     *
     * @param int    $outputMode (optional) output mode (XML, XHTML, HTML5 (see PHPTAL constants). Default XHTML.
     * @param string $encoding   (optional) charset encoding for template. Default UTF-8.
     * @param int    $lifetime   (optional) count of days to cache templates. Default 1 day.
     */
    public function configureEngine($outputMode = PHPTAL::XHTML, $encoding = 'UTF-8', $cache_lifetime_days = 1)
    {
        $this->setOutputMode($outputMode);
        $this->SetEncoding($encoding);
        $this->setLifetime($cache_lifetime_days);

        $this->renderer->setTemplateRepository(dirname(__FILE__).'/../view/');
        $this->tenderer->setPhpCodeDestination(dirname(__FILE__).'/../viewc/');

        if (DEBUG == true) {
            $this->tpl->setForceReparse(true);
        }
    }

    /**
     * Plug in PHPTAL object into View
     *
     * @param object PHPTAL $phptal
     */
    public function setEngine(PHPTAL $phptal)
    {
        $this->renderer = $phptal;
        // @todo check, if $this should be injected into phptal?
        #$this->renderer->set('this', $this);

        return $this;
    }

    /**
     * Returns a clean Smarty Object
     *
     * @return Smarty Object
     */
    public function getEngine()
    {
        return $this->renderer;
    }

    /**
     * Renders a template and returns the content.
     *
     * @param $template
     * @param $returnOutput
     * @return string Rendered Template Content
     */
    public function render($template, $viewdata = null)
    {
        // get the template from the parent class
        if ($template === null) {
            $template = $this->getTemplate();
        }

        if ($viewdata !== null) {
            $this->assign($viewdata);
        }

        $this->renderer->setTemplate($template);

        try {
            // let PHPTAL process the template
            return $this->renderer->execute();
        } catch (Exception $e) {
            throw new \Koch\Exception\Exception($e);
        }
    }

    /**
     * Renders a template and displays the content.
     *
     * @param $template
     * @param $returnOutput
     * @return string Rendered Template Content
     */
    public function display($template, $viewdata = null)
    {
        // get the template from the parent class
        if ($template === null) {
            $template = $this->getTemplate();
        }

        if ($viewdata !== null) {
            $this->assign($viewdata);
        }

        $this->renderer->setTemplate($template);

        try {
            // let PHPTAL process the template
            echo $this->renderer->execute();
        } catch (Exception $e) {
            throw new \Koch\Exception\Exception($e);
        }
    }

    /**
     * Fetches Template
     *
     * @param $template Template
     * @return string Rendered Template Content.
     */
    public function fetch($template, $data = null)
    {
        return $this->render($template, true);
    }

    /**
     * Set charset encoding
     *
     * @param string encoding
     */
    public function setEncoding($encoding)
    {
        return $this->renderer->setEncoding($encoding);
    }

    /**
     * Set output mode
     *
     * @param int output mode
     */
    public function setOutputMode($outputMode)
    {
        return $this->renderer->setOutputMode($outputMode);
    }

    /**
     * Set cache lifetime
     *
     * @param int lifetime in days
     */
    public function setLifetime($lifetime)
    {
        return $this->renderer->setCacheLifetime($lifetime);
    }
    /**
     * Set PHPTAL variables
     *
     * @param string $key   variable name
     * @param string $value variable value
     */
    public function __set($key, $value)
    {
        $this->renderer->assign($key, $value);
    }

    /**
     * Get PHPTAL Variable Value
     *
     * @param  string $key variable name
     * @return mixed  variable value
     */
    public function __get($key)
    {
        return $this->renderer->$key;
    }

    /**
     * Check if PHPTAL variable is set
     *
     * @param string $key variable name
     */
    public function __isset($key)
    {
        return isset($this->renderer->$key);
    }

    /**
     * Unset PHPTAL variable
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
