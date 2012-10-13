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

namespace Koch\Feed;

/**
 * A FeedElement is a part of a FeedGenerator feed.
 */
class FeedElement extends ElementBase
{
    /**
     * Mandatory attributes of an item.
     */
    public $title;
    public $description;
    public $link;

    /**
     * Optional attributes of an item.
     */
    public $author;
    public $authorEmail;
    public $image;
    public $category;
    public $comments;
    public $guid;
    public $source;
    public $creator;

    /**
     * Publishing date of an item. May be in one of the following formats:
     *
     *	RFC 822:
     *	"Mon, 20 Jan 03 18:05:41 +0400"
     *	"20 Jan 03 18:05:41 +0000"
     *
     *	ISO 8601:
     *	"2003-01-20T18:05:41+04:00"
     *
     *	Unix:
     *	1043082341
     */
    public $date;

    /**
     * Add <enclosure> element tag RSS 2.0
     * modified by : Mohammad Hafiz bin Ismail (mypapit@gmail.com)
     *
     *
     * display :
     * <enclosure length="17691" url="http://something.com/picture.jpg" type="image/jpeg" />
     *
     */
    public $enclosure;

    /**
     * Any additional elements to include as an assiciated array. All $key => $value pairs
     * will be included unencoded in the feed item in the form
     *     <$key>$value</$key>
     * Again: No encoding will be used! This means you can invalidate or enhance the feed
     * if $value contains markup. This may be abused to embed tags not implemented by
     * the FeedCreator class used.
     */
    public $additionalElements = array();
}
