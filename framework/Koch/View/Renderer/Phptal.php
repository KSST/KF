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
 */
class Phptal extends AbstractRenderer
{
    /* @var \PHPTAL */
    public $renderer = null;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
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
     * Render Engine Configuration
     * Configures the PHPTAL Object
     *
     * @param int    $outputMode (optional) output mode (XML, XHTML, HTML5 (see PHPTAL constants). Default XHTML.
     * @param string $encoding   (optional) charset encoding for template. Default UTF-8.
     */
    public function configureEngine($outputMode = PHPTAL::XHTML, $encoding = 'UTF-8', $cache_lifetime_days = 1)
    {
        $this->setOutputMode($outputMode);
        $this->SetEncoding($encoding);
        $this->setLifetime($cache_lifetime_days);

        $this->renderer->setTemplateRepository(__DIR__ . '/../../view/');
        $this->tenderer->setPhpCodeDestination(__DIR__ . '/../../viewc/');

        if (DEBUG == true) {
            $this->tpl->setForceReparse(true);
        }
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
     * Renders a template and returns the content.
     *
     * @param  string  $template
     * @param $returnOutput
     * @param  boolean $viewdata
     * @return string  Rendered Template Content
     */
    public function render($template = null, $viewdata = null)
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
}
