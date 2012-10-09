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
 * Class for Image Watermarking.
 *
 * @package     Koch
 * @subpackage  Core
 * @category    Image
 */
class Watermark extends Image
{
    public function __construct($function, $config)
    {
        if ($function == 'image') {

            $watermark = imagecreatefrompng($config['file']);

            imagecopy(
                $this->workImage,
                $watermark,
                $config['pos_x'],
                $config['pos_y'],
                0,
                0,
                imagesx($watermark),
                imagesy($watermark)
            );
        }
    }
}
