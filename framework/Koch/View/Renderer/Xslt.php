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
    /**
     * holds instance of XSLT Render Engine (object)
     *
     * @var object xslt
     */
    protected $xslt = null;

    /**
     * Absolute path to the XSL StyleSheet file.
     *
     * @var string
     */
    public $xslfile = null;

    /**
     * Constructor.
     *
     * @param \Koch\View\Renderer\Koch\Config $config
     */
    public function __construct(Koch\Config $config)
    {
        parent::__construct($config);

        // instantiate the render engine
        $this->xslt = new \XSLTProcessor;
    }

    /**
     * Returns XSLT RenderEngine Object
     *
     * @return xslt_processor
     */
    public function getEngine()
    {
        return $this->xslt;
    }

    /**
     * setXSLStyleSheet
     *
     * @param $xslfile The fullpath to the XSL StyleSheet file for later combination with the xml data.
     */
    public function setXSLStyleSheet($xslfile)
    {
        $this->xslfile = $xslfile;
    }

    /**
     * getXSLStyleSheet
     *
     * @return $xslfile
     */
    public function getXSLStyleSheet()
    {
        return $this->xslfile;
    }

    /**
     * This renders the xml $data array.
     *
     * @param $template The XSL Stylesheet.
     * @param $data XML Data to render
     */
    public function render($template, $data)
    {
        // $this->response()->setContentType('text/html');

        if($template != '') {
            $stylesheet = $template;
        } else {
            $stylesheet = $this->getXSLStyleSheet();
        }

        // import the stylesheet for later transformation
        $this->xslt->importStyleSheet(\DOMDocument::load($stylesheet));

        // then import the xml data (or file) into the XSLTProcessor and start the transform
        $dom = $this->xslt->transformToXML(\DOMDocument::load($data));

        return $dom;
    }

    public function assign($tpl_parameter, $value = null)
    {

    }

    public function configureEngine()
    {

    }

    public function display($template, $viewdata = null)
    {

    }

    public function fetch($template, $viewdata = null)
    {

    }

    public function initializeEngine($template = null)
    {

    }
}
