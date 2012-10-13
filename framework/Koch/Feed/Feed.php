<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Feed;

/**
 * Koch Framework - Class for Feed Handling
 *
 * This is a Dual-Wrapper for SimplePie and FeedCreator.
 *
 * This is a wrapper for the Feed-Reader Library SimplePie.
 * SimplePie is PHP-Based RSS and Atom Feed Framework.
 * It's written and copyrighted by Ryan Parman and Geoffrey Sneddon, (c) 2004-2008.
 * SimplePie is licensed under the modified BSD (3-clause) license.
 *
 * This is a wrapper for the Feed-Creator Library FeedCreator.
 * It's originally written and copyrighted by Kai Blankenhorn, extended by Scott Reynen, Dirk Clemens, (c).
 * FeedCreator is licensed under LGPL v2.1 or any later version.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Feed
 */
class Feed
{
    /**
     * Fetches a feed by URL and caches it - using the SimplePie Library.
     *
     * @param string $feed_url       This is the URL you want to parse.
     * @param int    $cache_duration This is the number of seconds that you want to store the feedcache file for.
     * @param string $cache_location This is where you want the cached feeds to be stored.
     *
     * @return object \SimplePie
     */
    public static function fetchRSS($feed_url, $numberOfItems = null, $cache_duration = null, $cache_location = null)
    {
        // load simplepie
        include __DIR__ . '/../../vendor/simplepie/SimplePie.php';

        // instantiate simplepie
        $simplepie = new \SimplePie();

        // if cache_location was not specified manually, set it to the default cache directory for feeds
        $cache_location = ($cache_location === null) ? APPLICATION_CACHE_PATH : $cache_location;

        // if cache_duration was not specified manually, set it to the default cache duration time of 1800
        $cache_duration = ($cache_duration == null) ? 1800 : $cache_duration;

        // if number of items to fetch is null, set it to the default value of 5 items
        $numberOfItems = ($numberOfItems == null) ? 5 : $numberOfItems;

        // finally: fetch the feed and cache it!
        $simplepie->set_feed_url($feed_url);
        $simplepie->set_cache_location($cache_location);
        $simplepie->set_cache_duration($cache_duration);
        $simplepie->set_timeout(5);
        $simplepie->set_output_encoding('UTF-8');
        $simplepie->set_stupidly_fast(true);
        $simplepie->init();
        $simplepie->handle_content_type();

        return $simplepie;
    }

    /**
     * Fetches a feed by URL and caches it.
     * Be advised to use the method fetchRSS() instead.
     *
     * @param string  $feed_url       This is the URL you want to parse.
     * @param boolean $cache          If true caches the content. Default true.
     * @param string  $cache_location This is where you want the cached feeds to be stored.
     *
     * @return string Feed content.
     */
    public static function fetchRawRSS($feed_url, $cache = true, $cache_location = null)
    {
        if ($cache === true) {

            // Cache Filename and Path
            $cachefile = ($cache_location == null) ? (APPLICATION_CACHE_PATH . md5($feed_url)) : $cache_location;

            // define cache lifetime
            $cachetime = 60*60*3; // 10800min = 3h
        }

        // try to return the file from cache
        if (true === $cache and is_file($cachefile) and (time() - filemtime($cachefile)) < $cachetime) {
            return file_get_contents($cachefile);
        } else { // get the feed from the source
            if ($cache === true) {
                // ensure cachefile exists, before we write
                touch($cachefile);
                chmod($cachefile, 0666);
            }

            // get Feed from source
            $feedcontent = file_get_contents($feed_url, FILE_TEXT);

            // ensure that we have rss content
            if (strlen($feedcontent) > 0) {
                // write cache file
                if ($cache === true) {
                    $fp = fopen($cachefile, 'w');
                    fwrite($fp, $feedcontent);
                    fclose($fp);
                }

                return $feedcontent;
            } else {
                return null;
            }
        }
    }
}
