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

/**
 * View Renderer for native PHP Templates.
 *
 * This is a wrapper/adapter for using native PHP as Template Engine.
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class Php extends Renderer_Base
{
    private $file;

    private $data = array();

    /**
     * Executes the template rendering and returns the result.
     *
     * @param  string $template Template Filename
     * @param  array  $data     Data to extract to the local scope.
     * @return string
     */
    public function fetch($filename = null, array $data = array())
    {
        if(is_array($data)) {
            $this->data = $data;
        }
        
        $file = '';

        if ($filename === null) {
            $file = $filename . '.tpl';
        } else {
            $file = $this->file;
        }

        /**
         * extract all template variables to local scope,
         * but do not overwrite an existing variable.
         * on collision, prefix variable with "invalid_".
         */
        extract($this->data, EXTR_REFS | EXTR_PREFIX_INVALID, 'invalid_');

        ob_start();

        try {
            include $file; // conditional include; not require !
        } catch (\Exception $e) {
            // clean buffer before throwing exception
            ob_get_clean();
            throw $e;
            // throw new Koch_Excpetion('PHP Renderer Error: Template ' . $file . ' not found!', 99);
        }

        return ob_get_clean();
    }

    /**
     * Assign specific variable to the template
     *
     * @param  mixed             $key   Object with template vars (extraction method fetch), or array or key/value pair
     * @param  mixed             $value Variable value
     * @return Koch_Renderer_PHP
     */
    public function assign($key, $value=null)
    {
        if (is_object($key)) {
            // @todo pull object props to array
            $this->data[$key] = $value->fetch();
        } elseif (is_array($key)) {
            array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Display the rendered template
     *
     * @return string HTML Representation of Template with Vars
     */
    public function render($template, $viewdata)
    {
        $this->assign($viewdata);

        return $this->fetch($template);
    }

    /**
     * Render the content and return it
     *
     * @example
     * echo new Koch_Renderer_PHP($file, array('title' => 'My title'));
     *
     * @return string HTML Representation
     */
    public function __toString()
    {
        return $this->render();
    }
}
