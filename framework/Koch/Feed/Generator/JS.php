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
