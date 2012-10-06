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
 * Class for Image Thumbnailing.
 *
 * @package     Koch
 * @subpackage  Core
 * @category    Image
 */
class Thumbnail extends Image
{
    protected $object;

    public function __construct($config, Koch_Image $object)
    {

        $this->object = $object;
        $this->object->thumbName = $config['thumb_name'];
        $this->object->newWidth = $config['new_width'];
        $this->object->newHeight = $config['new_height'];
        $this->object->keepAspectRatio = $config['keep_aspect_ratio'];
        $this->object->aspectRatio = $this->calcAspectRatio();
        $this->object->jpegQuality = $config['jpeg_quality'];
        $this->object->workImage = $object->getWorkImageResource($object->newWidth, $object->newHeight);
    }

    public function calcAspectRatio()
    {
        if ($this->object->newWidth != 0) {
            $ratio = $this->object->originalWidth / $this->object->newWidth;
            $this->object->newHeight = ((int) round($this->object->originalHeight / $ratio));

            return $ratio;
        }

        if ($this->object->newHeight != 0) {
            $ratio = $this->object->originalHeight / $this->object->newHeight;
            $this->object->newWidth((int) round($this->object->originalWidth / $ratio));

            return $ratio;
        }
    }
}
