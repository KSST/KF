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
     * Render serialized PHP data
     *
     * @param $template Unused.
     * @param $viewdata Data to serialize.
     *
     * @return string Serialized data.
     */
    public function render($template, $viewdata)
    {
        return serialize($this->viewdata);
    }
}
