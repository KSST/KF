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
     * @var object xslt
     */
    protected $xslt = null;

    /**
     * @var filepath to the XSL StyleSheet file
     */
    public $xslfile = null;

    /**
     * holds the abs path to the xsl stylesheet
     * @var string
     */
    protected $xslfile = null;

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
     * @param $data XML Data to render
     */
    public function render($data)
    {
        // $this->response()->setContentType('text/html');

        // import the stylesheet for later transformation
        $this->xslt->importStyleSheet(\DOMDocument::load($this->getXSLStyleSheet()));

        // then import the xml data (or file) into the XSLTProcessor and start the transform
        $dom = $this->xslt->transformToXML(\DOMDocument::load($data));

        return $dom;
    }
}
