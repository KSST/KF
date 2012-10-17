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
 * JSCreator is a class that writes a js file to a specific
 * location, overriding the createFeed method of the parent HTMLCreator.
 */
class JS extends HTML
{
    public $contentType = "text/javascript";

    /**
     * The file extension to be used in the cache file
     */
    protected $suffix = 'js';

    /**
     * writes the javascript
     * @return string the scripts's complete text
     */
    public function renderFeed()
    {
        $feed = parent::createFeed();
        $feedArray = explode("\n", $feed);

        $jsFeed = "";
        foreach ($feedArray as $value) {
            $jsFeed .= "document.write('".trim(addslashes($value))."');\n";
        }

        return $jsFeed;
    }

    /**
     * Overrrides parent to produce .js extensions
     *
     * @return string the feed cache filename
     */
    protected function generateFilename()
    {
        $fileInfo = pathinfo($_SERVER['PHP_SELF']);

        return substr($fileInfo["basename"], 0, -(strlen($fileInfo["extension"]) + 1)) . ".js";
    }
}
