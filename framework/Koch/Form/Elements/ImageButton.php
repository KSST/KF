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

namespace Koch\Form\Elements;

use Koch\Form\FormElementInterface;

class ImageButton extends Input implements FormElementInterface
{
    /**
     * width of image (px)
     *
     * @var int
     */
    public $width;

    /**
     * height of image (px)
     *
     * @var int
     */
    public $height;

    /**
     * URL of image
     *
     * @var string
     */
    public $source;

    public function __construct()
    {
        $this->type = 'image';
    }

    /**
     * sets URL of image
     *
     * @param string $source
     */
    public function setImageURL($source)
    {
        $this->source = $source;
    }

    /**
     * sets width and height of image (px)
     *
     * @param int $width
     * @param int $height
     */
    public function setDimensions($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
}
