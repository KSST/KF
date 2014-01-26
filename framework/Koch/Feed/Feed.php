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
 * Class for reading feeds via SimplePie (wrapper).
 *
 * This is a wrapper for the Feed-Reader library SimplePie, a RSS and Atom Feed Framework.
 * It's written and copyrighted by Ryan Parman and Geoffrey Sneddon, (c) 2004-2008.
 * SimplePie is licensed under the modified BSD (3-clause) license.
 * @link https://github.com/simplepie/simplepie
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
        if (false === class_exists('SimeplePie')) {
            throw new \Exception('This class requires the vendor library "SimplePie".');
        }

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
     * @param string  $feedUrl       This is the URL you want to parse.
     * @param boolean $cache         If true caches the content. Default true.
     * @param string  $cacheLocation The path, where you want the cached feeds to be stored.
     *
     * @return string Feed content.
     */
    public static function fetchRawRSS($feedUrl, $cache = true, $cacheLocation = null)
    {
        if ($cache === true) {

            // Cache Filename and Path
            $cacheLocation = ($cacheLocation == null) ? APPLICATION_CACHE_PATH : $cacheLocation;

            // attach cache filename
            $cachefile = $cacheLocation . md5($feedUrl);

            // define cache lifetime
            $cachetime = 60*60*3; // 10800min = 3h
        }

        // try to return the file from cache
        if (true === $cache and is_file($cachefile) and (time() - filemtime($cachefile)) < $cachetime) {
            return file_get_contents($cachefile);
        } else { // get the feed from the source
            try {
                // to get feed from source
                $feedcontent = file_get_contents($feedUrl, FILE_TEXT);

                // ensure that we have rss content, before writing cache file
                if ((strlen($feedcontent) > 0) and ($cache === true)) {
                    // write cache file
                    file_put_contents($cachefile, $feedcontent);
                }

                return $feedcontent;
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
