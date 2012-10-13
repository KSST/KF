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

namespace Koch\Feed\Element;

use Koch\Feed\ElementBase;

/**
 * An FeedImage may be added to a FeedCreator feed.
 */
class Image extends ElementBase
{
    /**
     * Mandatory attributes of an image.
     */
    public $title;
    public $url;
    public $link;

    /**
     * Optional attributes of an image.
     */
    public $width;
    public $height;
    public $description;
}
