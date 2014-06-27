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
 */

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - View Renderer for serialized PHP data.
 *
 * This is a wrapper/adapter for returning serialized PHP data.
 */
class Serialized extends AbstractRenderer
{
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
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
     * Render serialized PHP data
     *
     * @param $template Unused.
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
     * Assign specific variable to the template
     *
     * @param  mixed  $key   Object with template vars (extraction method fetch), or array or key/value pair
     * @param  string  $value Variable value
     * @return Serialized \Koch\View\Renderer\Serialized
     */
    public function assign($key, $value = null)
    {
        if (is_object($key) === true) {
            // pull all non-static object properties
            $this->viewdata = get_object_vars($key);
        } elseif (is_array($key) === true) {
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
