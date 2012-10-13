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

class Enclosure extends ElementBase
{
    /**
     * core variables
     */
    public $url;
    public $length;
    public $type;

    /**
     * For use with another extension like Yahoo mRSS
     *
     * Warning : These variables might not show up in later release / not finalize yet!
     */
    public $width;
    public $height;
    public $title;
    public $description;
    public $keywords;
    public $thumburl;

    public $additionalElements = array();
}
