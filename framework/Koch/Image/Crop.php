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

namespace Koch\Image;

/**
 * Class for Image Cropping.
 */
class Crop extends Image
{

    public function __construct($config)
    {
        $this->startX = $config['start_x'];
        $this->startY = $config['start_y'];
        $this->newWidth = $config['width'];
        $this->newHeight = $config['height'];
        $this->originalWidth = $config['width'];
        $this->originalHeight = $config['height'];
        $this->jpegQuality = 100;

        $this->workImage = $this->getWorkImageResource($this->newWidth, $this->newHeight);
    }
}
