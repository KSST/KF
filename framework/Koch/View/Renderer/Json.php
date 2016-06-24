<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
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
 *
 * @see http://www.ietf.org/rfc/rfc4627.
 *
 * This class implements two ways of rendering data as json.
 * 1) The method renderByHeader() wraps the json directly in the header.
 * 2) The method render() returns the json data for later rendering (as body).
 */
class Json extends AbstractRenderer
{
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
    }

    public function initializeEngine($template = null)
    {
        return;
    }

    public function configureEngine()
    {
        return;
    }

    /**
     * jsonEncode.
     *
     * @param mixed|array $data The data to be json encoded.
     *
     * @return $json_encoded_data
     */
    public function jsonEncode($data = null)
    {
        return ($data === null) ? '[]' : json_encode($data);
    }

    /**
     * Render PHP data as X-JSON HEADER.
     * This method does not return the json encoded string for rendering,
     * but sets it directly to the header.
     *
     * @param $data array php-array
     */
    public function renderAsHeader($data)
    {
        \Koch\Http\HttpResponse::addHeader('X-JSON', '(' . $this->jsonEncode($data) . ')');

        return true;
    }

    public function assign($tpl_parameter, $value = null)
    {
        $this->viewdata[$tpl_parameter] = $value;
    }

    public function display($template, $viewdata = null)
    {
        echo $this->render($template, $viewdata);
    }

    public function fetch($template, $viewdata = null)
    {
        return $this->render($template, $viewdata);
    }

    /**
     * Render PHP data as JSON (through BODY)
     * This method returns the json encoded string.
     *
     * @param $data array
     * @param string $template
     *
     * @return $json_encoded_data
     */
    public function render($template = null, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->viewdata = $viewdata;
        }

        /*
         * Defense against MIME content-sniffing attacks. Supported by IE and Chrome only.
         * For all other browsers: escape properly or force content disposition header,
         * to open a download box on client side (as a warning).
         */
        \Koch\Http\HttpResponse::addHeader('X-Content-Type-Options', 'nosniff');

        /*
         * The MIME media type for JSON text is application/json.
         * @see http://www.ietf.org/rfc/rfc4627
         */
        \Koch\Http\HttpResponse::addHeader('Content-Type', 'application/json; charset=UTF-8');

        $json = $this->jsonEncode($this->viewdata);

        \Koch\Http\HttpResponse::addHeader('Content-Length', mb_strlen($json));

        return $json;
    }
}
