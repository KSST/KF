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
 * View Renderer for serialized PHP data.
 *
 * This is a wrapper/adapter for returning serialized PHP data.
 */
class Serialized extends AbstractRenderer
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
     * Render serialized PHP data.
     *
     * @param string $template Unused.
     * @param $viewdata Data to serialize.
     *
     * @return string Serialized data.
     */
    public function render($template = null, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->viewdata = $viewdata;
        }

        return serialize($this->viewdata);
    }

    /**
     * Assign specific variable to the template.
     *
     * @param mixed  $key   Object with template vars (extraction method fetch), or array or key/value pair
     * @param string $value Variable value
     *
     * @return Serialized \Koch\View\Renderer\Serialized
     */
    public function assign($key, $value = null)
    {
        if (is_object($key)) {
            // pull all non-static object properties
            $this->viewdata = get_object_vars($key);
        } elseif (is_array($key)) {
            $this->viewdata += $key;
        } else {
            $this->viewdata[$key] = $value;
        }

        return $this;
    }

    public function display($template = null, $viewdata = null)
    {
        echo $this->render($template, $viewdata);
    }

    public function fetch($template = null, $viewdata = null)
    {
        return $this->render($template, $viewdata);
    }
}
