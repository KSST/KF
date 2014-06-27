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
 */

namespace Koch\Feed;

/**
 * FeedCreator lets you choose during runtime which format to build.
 */
class Generator // extends AbstractGenerator
{
    protected $feed = null;
    protected $format = '';

    /**
     * @param string $format
     */
    protected function setMIME($format)
    {
        switch (strtoupper($format)) {
            case '2.0':
                // fall through
            case 'RSS2.0':
                header('Content-type: text/xml', true);
                break;
            case '1.0':
                // fall through
            case 'RSS1.0':
                header('Content-type: text/xml', true);
                break;
            case 'PIE0.1':
                header('Content-type: text/xml', true);
                break;
            case 'MBOX':
                header('Content-type: text/plain', true);
                break;
            case 'OPML':
                header('Content-type: text/xml', true);
                break;
            case 'ATOM':
                // fall through: always the latest ATOM version
            case 'ATOM1.0':
                header('Content-type: application/xml', true);
                break;
            case 'ATOM0.3':
                header('Content-type: application/xml', true);
                break;
            case 'HTML':
                header('Content-type: text/html', true);
                break;
            case 'JS':
                // fall through
            case 'JAVASCRIPT':
                header('Content-type: text/javascript', true);
                break;
            default:
            case '0.91':
                // fall through
            case 'RSS0.91':
                header('Content-type: text/xml', true);
                break;
        }
    }

    /**
     * @param string $format
     */
    protected function setFormat($format)
    {
        switch (strtoupper($format)) {
            case "2.0":
                $this->format = 'RSS2.0';
                // fall through
            case "RSS2.0":
                $this->feed = new \Koch\Feed\Generator\RSS20();
                break;
            case "1.0":
                $this->format = 'RSS1.0';
                // fall through
            case "RSS1.0":
                $this->feed = new \Koch\Feed\Generator\RSS10();
                break;
            case "0.91":
                $this->format = 'RSS0.91';
                // fall through
            case "RSS0.91":
                $this->feed = new \Koch\Feed\Generator\RSS091();
                break;
            case "PIE0.1":
                $this->feed = new \Koch\Feed\Generator\PIE01();
                break;
            case "MBOX":
                $this->feed = new \Koch\Feed\Generator\MBOX();
                break;
            case "OPML":
                $this->feed = new \Koch\Feed\Generator\OPML();
                break;
            case "ATOM":
                // fall through: always the latest ATOM version
            case "ATOM1.0":
                $this->feed = new \Koch\Feed\Generator\Atom10();
                break;
            case "HTML":
                $this->feed = new \Koch\Feed\Generator\HTML();
                break;
            case "JS":
                $this->format = 'JAVASCRIPT';
                // fall through
            case "JAVASCRIPT":
                $this->feed = new \Koch\Feed\Generator\JS();
                break;
            default:
                $this->feed = new \Koch\Feed\Generator\RSS091();
                break;
        }

        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            // prevent overwriting of properties "contentType", "encoding"; do not copy "_feed" itself
            if (!in_array($key, array("_feed", "contentType", "encoding"))) {
                $this->feed->{$key} = $this->{$key};
            }
        }
    }

    /**
     * Creates a syndication feed based on the items previously added.
     *
     * @see        FeedCreator::addItem()
     * @param    string    format    format the feed should comply to. Valid values are:
     * "PIE0.1", "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM1.0", "HTML", "JS"
     * @return string the contents of the feed.
     */
    public function createFeed($format = 'RSS0.91')
    {
        $this->setFormat($format);

        return $this->feed->renderFeed();
    }

    /**
     * Saves this feed as a file on the local disk. After the file is saved, an HTTP redirect
     * header may be sent to redirect the use to the newly created file.
     *
     * @param string format format the feed should comply to. Valid values are:
     * "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM1.0", "HTML", "JS"
     * @param string filename optional the filename where a recent version of the feed is saved.
     * If not specified, the filename is $_SERVER['PHP_SELF'] with
     * the extension changed to .xml (see _generateFilename()).
     * @param boolean displayContents optional send the content of the file or not.
     * If true, the file will be sent in the body of the response.
     */
    public function saveFeed($format = 'RSS0.91', $filename = '', $displayContents = true)
    {
        if ($format) {
            $this->setFormat($format);
        }
        $this->feed->saveFeed($filename, $displayContents);
    }

    /**
     * Turns on caching and checks if there is a recent version of this feed in the cache.
     * If there is, an HTTP redirect header is sent.
     * To effectively use caching, you should create the FeedCreator object and call this method
     * before anything else, especially before you do the time consuming task to build the feed
     * (web fetching, for example).
     *
     * @param   string   format   format the feed should comply to. Valid values are:
     *       "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM1.0".
     * @param filename   string   optional the filename where a recent version of the feed is saved.
     * If not specified, the filename is $_SERVER['PHP_SELF'] with
     * the extension changed to .xml (see _generateFilename()).
     * @param timeout int optional the timeout in seconds before a cached version is refreshed (default 3600 = 1h)
     */
    public function useCached($format = 'RSS0.91', $filename = '', $timeout = 3600)
    {
        if ($format) {
            $this->setFormat($format);
        }
        $this->feed->useCached($filename, $timeout);
    }

    /**
     * Outputs feed to the browser - needed for on-the-fly feed generation (like it is done in WordPress, etc.)
     *
     * @param format string format the feed should comply to. Valid values are:
     * "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3".
     */
    public function outputFeed($format = 'RSS0.91')
    {
        $this->setFormat($format);
        $this->setMIME($format);
        $this->feed->outputFeed();
    }
}
