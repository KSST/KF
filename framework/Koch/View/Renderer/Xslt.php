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
 * Koch Framework - View Renderer for XSLT/XML.
 *
 * This is a wrapper/adapter for returning XML/XSLT data.
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Xslt extends AbstractRenderer
{
    /* @var \XSLTProcessor */
    public $renderer = null;

    /**
     * Absolute path to the XSL StyleSheet file.
     *
     * @var string
     */
    public $xslfile = null;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (extension_loaded('libxml') === false or extension_loaded('xsl') === false) {
            throw new Exception(
                'The PHP extension libxml is not loaded. You may enable it in "php.ini" (extension=php_xsl.dll)!'
            );
        }

        parent::__construct($options);

        // instantiate the render engine
        // @link http://php.net/manual/en/class.xsltprocessor.php
        $this->renderer = new \XSLTProcessor;
    }

    public function initializeEngine($template = null)
    {

    }

    public function configureEngine()
    {

    }

    /**
     * Set XSL Stylesheet
     *
     * @param $xslfile The fullpath to the XSL StyleSheet file for later combination with the xml data.
     */
    public function setStylesheet($xslfile)
    {
        $this->xslfile = $xslfile;
    }

    /**
     * Get XSL Stylesheet
     *
     * @return $xslfile
     */
    public function getStylesheet()
    {
        return $this->xslfile;
    }

    /**
     * This renders the xml $data array.
     *
     * @param $template The XSL Stylesheet.
     * @param $data XML Data to render
     */
    public function render($template, $viewdata = null)
    {
        // $this->response()->setContentType('text/html');

        if (!empty($this->xslfile)) {
            $dom_stylesheet = new \DOMDocument;
            $dom_stylesheet->load($this->xslfile);
            // import the stylesheet for later transformation
            $this->renderer->importStyleSheet($dom_stylesheet);
        }

        $dom_xml = new \DOMDocument;
        $dom_xml->load($template);

        // then import the xml data (or file) into the XSLTProcessor and start the transform
        $dom = $this->renderer->transformToXML($dom_xml);

        echo $dom;
    }

    public function assign($tpl_parameter, $value = null)
    {

    }

    public function display($template, $viewdata = null)
    {

    }

    public function fetch($template, $viewdata = null)
    {
    }
}
