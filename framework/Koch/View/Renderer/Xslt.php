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
 * View Renderer for XSLT/XML.
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
    public function render($template = null, $viewdata = null)
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
        return $this->renderer->transformToXML($dom_xml);
    }

    public function assign($tpl_parameter, $value = null)
    {
        return;
    }

    public function display($template, $viewdata = null)
    {
        echo $this->render($template, $viewdata);
    }

    public function fetch($template, $viewdata = null)
    {
        return $this->render($template, $viewdata);
    }
}
