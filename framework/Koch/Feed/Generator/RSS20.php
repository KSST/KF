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

namespace Koch\Feed\Generator;

/**
 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
 *
 * @see http://cyber.law.harvard.edu/rss/rss.html
 */
class RSS20 extends RSS091
{
    public function __construct($identifier = '')
    {
        parent::__construct($identifier);
        parent::_setRSSVersion('2.0" xmlns:atom="http://www.w3.org/2005/Atom');
    }
}
