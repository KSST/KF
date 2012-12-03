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
    public function __construct(\Koch\Config\Config $config)
    {
        if (extension_loaded('libxml') === false or
            extension_loaded('xsl') === false) {
            throw new Exception(
                'The PHP extension libxml is not loaded. You may enable it in "php.ini" (extension=php_xsl.dll)!'
            );
        }

        parent::__construct($config);

        // instantiate the render engine
        // @link http://php.net/manual/en/class.xsltprocessor.php
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

        if ($template != '') {
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
