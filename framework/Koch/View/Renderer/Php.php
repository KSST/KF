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
 * View Renderer for native PHP Templates.
 *
 * This is a wrapper/adapter for using native PHP as Template Engine.
 */
class Php extends AbstractRenderer
{
    private $file;

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
     * Assign specific variable to the template
     *
     * @param  mixed                   $key   Object with template vars (extraction method fetch), or array or key/value pair
     * @param  string                  $value Variable value
     * @return \Koch\View\Renderer\PHP
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

    public function display($template, $viewdata = null)
    {
        $this->assign($viewdata);

        echo $this->render($template);
    }

    /**
     * Executes the template rendering and returns the result.
     *
     * @param  string $template Template Filename
     * @return string
     */
    public function fetch($template, $viewdata = null)
    {
        $this->assign($viewdata);

        return $this->render($template);
    }

    /**
     * Display the rendered template
     *
     * @return string HTML Representation of Template with Vars
     */
    public function render($template = null, $viewdata = null)
    {
        $this->assign($viewdata);

        $this->file = $template;

        /**
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
