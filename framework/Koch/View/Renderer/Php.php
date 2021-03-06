<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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
 * Koch Framework - View Renderer for native PHP Templates.
 *
 * This is a wrapper/adapter for using native PHP as Template Engine.
 */
class Php extends AbstractRenderer
{
    private $file;

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
     * Assign specific variable to the template.
     *
     * @param mixed  $key   Object with template vars (extraction method fetch), or array or key/value pair
     * @param string $value Variable value
     *
     * @return \Koch\View\Renderer\PHP
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

    public function display($template, $viewdata = null)
    {
        $this->assign($viewdata);

        echo $this->render($template);
    }

    /**
     * Executes the template rendering and returns the result.
     *
     * @param string $template Template Filename
     *
     * @return string
     */
    public function fetch($template, $viewdata = null)
    {
        $this->assign($viewdata);

        return $this->render($template);
    }

    /**
     * Display the rendered template.
     *
     * @return string HTML Representation of Template with Vars
     */
    public function render($template = null, $viewdata = null)
    {
        $this->assign($viewdata);

        $this->file = $template;

        /*
         * extract all template variables to local scope,
         * but do not overwrite an existing variable.
         * on collision, prefix variable with "invalid_".
         */
        extract($this->viewdata, EXTR_REFS | EXTR_PREFIX_INVALID, 'invalid_');

        try {
            ob_start();
            $included = include $this->file;
            if ($included === false) {
                throw new \Koch\Exception\Exception(sprintf('Including the template "%s" failed.', $this->file));
            }

            return ob_get_clean();
        } catch (\Exception $ex) {
            ob_end_clean();
            throw $ex;
        }
    }
}
