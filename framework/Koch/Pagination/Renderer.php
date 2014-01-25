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

namespace Koch\Pagination;

class Renderer
{
    public $style;
    public $options = array();
    public $adapter;

    /**
     * Constructor.
     *
     * @param string $style
     * @param array  $options
     * @param object Pagination with Adapter
     */
    public function __construct($style = null, $options = null, $adapter)
    {
        $this->adapter = $adapter;
        $this->style = $this->factory($style, $options);
    }

    /**
     * Returns the classname of a pagination renderer by it's shortcut name.
     *
     * @staticvar array $viewRendererClassMap
     * @param  string $style Name of Pagination Renderer. Default "classic".
     * @return string Filename
     */
    public function getStyleClassname($style)
    {
        // use 'classic' as fallback style
        $style = ($style === null) ? 'classic' : $style;

        static $viewRendererClassMap = array(
          'classic' => 'Classic',
          'digg' => 'Digg',
          'extended' => 'Extended',
          'punbb' => 'PunBB'
        );

        return '\Koch\Pagination\Style\\' . $viewRendererClassMap[$style];
    }

    public function factory($style = null, $options = null)
    {
        $style = isset($style) ? $style : $this->style;
        $options = isset($options) ? $options : $this->options;

        $class = $this->getStyleClassname($style);

        return new $class($options);
    }

    public function render()
    {
        return $this->style->render($this->adapter);
    }
}
