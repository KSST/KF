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
 * View Renderer for LESS Styles Renderer.
 *
 * This class is a wrapper for the Less Compiler.
 *
 * Composer: { "require": { "leafo/lessphp": "0.4.0" }
 *
 * @link http://leafo.net/lessphp/
 * @link http://leafo.net/lessphp/docs/
 */
class Less extends AbstractRenderer
{
    /* @var \LessC */
    public $renderer = null;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->initializeEngine();

        $this->configureEngine();
    }

    public function initializeEngine($template = null)
    {
        // initialize the LESS compiler as renderer
        $this->renderer = new \lessc;
    }

    public function configureEngine()
    {
        ;
    }

    public function fetch($template, $viewdata = null)
    {
        ;
    }

    public function render($template = null, $viewdata = null)
    {
        try {
            $this->renderer->compile();
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function assign($tpl_parameter, $value = null)
    {
        ;
    }

    public function display($template, $viewdata = null)
    {
        ;
    }

}
