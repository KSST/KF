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
 * View Renderer for serialized PHP data.
 *
 * This is a wrapper/adapter for returning serialized PHP data.
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
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

    }

     public function configureEngine()
    {

    }

    /**
     * Render serialized PHP data
     *
     * @param $template Unused.
     * @param $viewdata Data to serialize.
     *
     * @return string Serialized data.
     */
    public function render($template, $viewdata = null)
    {
        if ($viewdata !== null) {
            $this->viewdata = $viewdata;
        }

        return serialize($this->viewdata);
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
}
