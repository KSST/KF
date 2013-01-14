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
 *
 */

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - View Renderer for Text Templates.
 *
 * This is a simple Text Template Engine.
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class TextTemplate extends AbstractRenderer
{
    /**
     * Sets the template file.
     *
     * @param  string $file
     * @throws InvalidArgumentException
     */
    public function setTemplate($file)
    {
        if (file_exists($file)) {
            $this->template = file_get_contents($file);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Template file "%s" not found.', $file)
            );
        }
    }

    /**
     * Renders the template and returns it.
     *
     * @param string Template File.
     * @param array Viewdata
     * @return string
     */
    public function render($template = null, $viewdata = null)
    {
        if($template !== null) {
            $this->setTemplate($template);
        }
        if($viewdata !== null) {
            $this->assign($viewdata);
        }

        $keys = array();

        // transform viewdata keys into placeholders
        foreach ($this->viewdata as $key => $value) {
            $keys[] = '{' . $key . '}';
        }

        // replace placeholders with values
        return str_replace($keys, $this->viewdata, $this->template);
    }

    /**
     * Renders template content to file.
     *
     * @param string $file Output file.
     * @return bool
     */
    public function renderToFile($file)
    {
        return (bool) file_put_contents($file, $this->render());
    }
}
