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
 * FeedCreator is the abstract base implementation for concrete
 * implementations of specific formats of syndication.
 */
abstract class GeneratorAbstract extends ElementBase
{
    /**
     * An identifier to be used to create the name for a cache file
     */
    protected $identifier = '';

    /**
     * The file extension to be used in the cache file
     */
    protected $suffix = 'xml';

    /**
     * Mandatory attributes of a feed.
     */
    public $title;
    public $description;
    public $link;

    /**
     * Optional attributes of a feed.
     */
    public $syndicationURL;
    public $image;
    public $language;
    public $copyright;
    public $pubDate;
    public $lastBuildDate;
    public $editor;
    public $editorEmail;
    public $webmaster;
    public $category;
    public $docs;
    public $ttl;
    public $rating;
    public $skipHours;
    public $skipDays;

    /**
    * The url of the external xsl stylesheet used to format the naked rss feed.
    * Ignored in the output when empty.
    */
    public $xslStyleSheet = "";

    /**
    * The url of the external xsl stylesheet used to format the naked rss feed.
    * Ignored in the output when empty.
    */
    public $cssStyleSheet = "";

    public $items = Array();

    /**
     * This feed's MIME content type.
     */
    protected $contentType = "application/xml";


    /**
     * This feed's character encoding.
     */
    public $encoding = "UTF-8";

    /**
     * Any additional elements to include as an assiciated array. All $key => $value pairs
     * will be included unencoded in the feed in the form
     *     <$key>$value</$key>
     * Again: No encoding will be used! This means you can invalidate or enhance the feed
     * if $value contains markup. This may be abused to embed tags not implemented by
     * the FeedCreator class used.
     */
    public $additionalElements = Array();

    public function __construct($identifier = '')
    {
        $this->identifier = $identifier ? $identifier : 'RSSfeed' . uniqid();

        // your local timezone, set to "" to disable or for GMT
        if (!defined('TIME_ZONE')) {
            // Obtain offset from the language/international system
            $offset = '+00:00';
            define('TIME_ZONE', '+00:00');
        }

        defined('FEEDGENERATOR_VERSION') || define('FEEDGENERATOR_VERSION', 'FeedCreator 1.7.2');
    }

    /**
     * Adds an FeedItem to the feed.
     *
     * @param object FeedItem $item The FeedItem to add to the feed.
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * Truncates a string to a certain length at the most sensible point.
     * First, if there's a '.' character near the end of the string, the string is truncated after this character.
     * If there is no '.', the string is truncated after the last ' ' character.
     * If the string is truncated, " ..." is appended.
     * If the string is already shorter than $length, it is returned unchanged.
     *
     * @param string    string A string to be truncated.
     * @param int        length the maximum length the string should be truncated to
     *
     * @return string the truncated string
     */
    public static function iTrunc($string, $length)
    {
        if (strlen($string) <= $length) {
            return $string;
        }

        $pos = strrpos($string, '.');
        if ($pos >= $length - 4) {
            $string = substr($string, 0, $length - 4);
            $pos = strrpos($string, '.');
        }
        if ($pos >= $length * 0.4) {
            return substr($string, 0, $pos + 1) . ' ...';
        }

        $pos = strrpos($string, ' ');
        if ($pos >= $length - 4) {
            $string = substr($string, 0, $length - 4);
            $pos = strrpos($string, ' ');
        }
        if ($pos >= $length * 0.4) {
            return substr($string, 0, $pos) . ' ...';
        }

        return substr($string, 0, $length - 4) . ' ...';
    }


    /**
     * Creates a comment indicating the generator of this feed.
     * The format of this comment seems to be recognized by Syndic8.com.
     */
    protected function createGeneratorComment()
    {
        return '<!-- generator="' . FEEDGENERATOR_VERSION . "\" -->\n";
    }

    /**
     * Creates a string containing all additional elements specified in
     * $additionalElements.
     *
     * @param	elements	array	an associative array containing key => value pairs
     * @param indentString	string	a string that will be inserted before every generated line
     *
     * @return string the XML tags corresponding to $additionalElements
     */
    protected function createAdditionalElements($elements, $indentString = "")
    {
        $ae = '';
        if (is_array($elements)) {
            foreach ($elements as $key => $value) {
                $ae.= $indentString . "<$key>$value</$key>\n";
            }
        }

        return $ae;
    }

    protected function createStylesheetReferences()
    {
        $xml = '';
        
        if (!empty($this->cssStyleSheet)) {
            $xml .= '<?xml-stylesheet href="' . $this->cssStyleSheet . "\" type=\"text/css\"?>\n";
        }
        
        if (!empty($this->xslStyleSheet)) {
            $xml .= '<?xml-stylesheet href="' . $this->xslStyleSheet . "\" type=\"text/xsl\"?>\n";
        }

        return $xml;
    }

    /**
     * Builds the feed's text.
     *
     * @return string the feed's complete text
     */
    abstract public function renderFeed();

    /**
     * Generate a filename for the feed cache file.
     * The result will be $_SERVER['PHP_SELF'] with the extension changed to .xml.
     * For example:
     *
     * echo $_SERVER['PHP_SELF']."\n";
     * echo FeedCreator::_generateFilename();
     *
     * would produce:
     *
     * /rss/latestnews.php
     * latestnews.xml
     *
     * @return string the feed cache filename
     */
    protected function generateFilename()
    {
        $fileInfo = pathinfo($_SERVER['PHP_SELF']);

        return substr($fileInfo['basename'], 0, -(strlen($fileInfo['extension']) + 1)) . '.xml';
    }

    protected function redirect($filename)
    {
        // attention, heavily-commented-out-area

        // maybe use this in addition to file time checking
        //Header("Expires: ".date("r",time()+$this->_timeout));

        /* no caching at all, doesn't seem to work as good:
        Header("Cache-Control: no-cache");
        Header("Pragma: no-cache");
        */

        // HTTP redirect, some feed readers' simple HTTP implementations don't follow it
        //Header("Location: ".$filename);

        //header("Content-Type: ".$this->contentType."; charset=".$this->encoding."; filename=".basename($filename));
        header('Content-Type: ' . $this->contentType . '; charset=' . $this->encoding);
        header('Content-Disposition: inline; filename=' . basename($filename));
        readfile($filename, 'r');
        die();
    }

    /**
     * Turns on caching and checks if there is a recent version of this feed in the cache.
     * If there is, an HTTP redirect header is sent.
     * To effectively use caching, you should create the FeedCreator object and call this method
     * before anything else, especially before you do the time consuming task to build the feed
     * (web fetching, for example).
     *
     * @param filename string optional the filename where a recent version of the feed is saved. 
     * If not specified, the filename is $_SERVER['PHP_SELF'] with the extension changed to .xml 
     * (see generateFilename()).
     * @param timeout int optional the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1h)
     */
    public function useCached($filename = '', $timeout = 3600)
    {
        $this->timeout = $timeout;
        if ($filename == '') {
            $filename = $this->generateFilename();
        }
        if (file_exists($filename) AND (time() - filemtime($filename) < $timeout)) {
            $this->redirect($filename);
        }
    }


    /**
     * Saves this feed as a file on the local disk. After the file is saved, a redirect
     * header may be sent to redirect the user to the newly created file.
     *
     * @param filename string optional the filename where a recent version of the feed is saved.
     * If not specified, the filename is $_SERVER['PHP_SELF'] with the extension changed to .xml
     * (see generateFilename()).
     * @param redirect boolean optional send an HTTP redirect header or not.
     * If true, the user will be automatically redirected to the created file.
     */
    public function saveFeed($filename = '', $displayContents = true)
    {
        if ($filename == '') {
            $filename = $this->generateFilename();
        }
        $feedFile = fopen($filename, 'w+');
        if ($feedFile) {
            fputs($feedFile, $this->renderFeed());
            fclose($feedFile);
            if ($displayContents) {
                $this->redirect($filename);
            }
        } else {
            throw new Exception('Error creating feed file, please check write permissions.');
        }
    }

    /**
     * Outputs this feed directly to the browser - for on-the-fly feed generation
     *
     * still missing: proper header output - currently you have to add it manually
     */
    public function outputFeed()
    {
        echo $this->renderFeed();
    }
}
