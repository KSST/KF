<?php

namespace Koch\Feed\Generator;

use Koch\Feed\Generator;

/**
 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
 *
 * @see http://backend.userland.com/rss
 */
class RSS20 extends RSS091
{
    public function __construct($identifier = '')
    {
        parent::__construct($identifier);
        parent::_setRSSVersion('2.0" xmlns:atom="http://www.w3.org/2005/Atom');
    }
}