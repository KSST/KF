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
 * View Renderer for JSON data.
 *
 * This is a wrapper/adapter for returning JSON data.
 *
 * JSON stands for JavaScript Object Notation (JSON).
 * It's an lightweight, text-based, language-independent data interchange format.
 * It was derived from the ECMAScript Programming Language Standard.
 * JSON defines formatting rules for the portable representation of structured data.
 * @see http://www.ietf.org/rfc/rfc4627.
 *
 * This class implements two ways of rendering data as json.
 * 1) The method renderByHeader() wraps the json directly in the header.
 * 2) The method render() returns the json data for later rendering (as body).
 *
 * @category    Koch
 * @package     Core
 * @subpackage  View
 */
class Json extends AbstractRenderer
{
    public function initializeEngine($template = null)
    {
    }

    public function configureEngine()
    {
    }

    /**
     * jsonEncode
     *
     * @param mixed|array $data Data to be json encoded.
     * @return $json_encoded_data
     */
    public function jsonEncode($data)
    {
        if (empty($data)) {
            return '[]';
        } else {
            // use php's json encode to modifiy data representation
            return json_encode($data);
        }
    }

    /**
     * Render PHP data as JSON (through HEADER)
     * This method does not return the json encoded string for rendering,
     * instead it applies it directly to the header.
     *
     * @param $data array php-array
     */
    public function renderByHeader($data)
    {
        $this->response->addHeader('X-JSON', '('.$this->jsonEncode($data).')');

        return;
    }

    /**
     * Render PHP data as JSON (through BODY)
     * This method returns the json encoded string.
     *
     * @param $data array
     * @return $json_encoded_data
     */
    public function render($template, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->assign($viewdata);
        }

        /**
         * The MIME media type for JSON text is application/json.
         * @see http://www.ietf.org/rfc/rfc4627
         */
        $this->response->addHeader(
            'Content-Type',
            'application/json; charset='.$this->config['locale']['outputcharset']
        );

        return $this->jsonEncode($this->viewdata);
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

    public function getEngine()
    {

    }
}
