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
 * Koch Framework - View Renderer for LESS Styles Renderer.
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
