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

/**
 * Koch Framework - View Renderer for PHPTAL templates.
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
}
