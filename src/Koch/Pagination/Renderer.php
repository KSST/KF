<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Pagination;

class Renderer
{
    public $style;
    public $options = [];
    public $adapter;

    /**
     * Constructor.
     *
     * @param string $style
     * @param array  $options
     * @param object Pagination with Adapter
     * @param Pagination $adapter
     */
    public function __construct($style = null, $options = null, $adapter = null)
    {
        $this->adapter = $adapter;
        $this->style   = $this->factory($style, $options);
    }

    /**
     * Returns the classname of a pagination renderer by it's shortcut name.
     *
     * @staticvar array $viewRendererClassMap
     *
     * @param string $style Name of Pagination Renderer. Default "classic".
     *
     * @return string Filename
     */
    public function getStyleClassname($style)
    {
        // use 'classic' as fallback style
        $style ??= 'classic';

        static $viewRendererClassMap = [
          'classic'  => 'Classic',
          'digg'     => 'Digg',
          'extended' => 'Extended',
          'punbb'    => 'PunBB',
        ];

        return '\Koch\Pagination\Style\\' . $viewRendererClassMap[$style];
    }

    /**
     * @param string $style
     */
    public function factory($style = null, $options = null)
    {
        $style ??= $this->style;
        $options ??= $this->options;

        $class = $this->getStyleClassname($style);

        return new $class($options);
    }

    public function render()
    {
        return $this->style->render($this->adapter);
    }
}
