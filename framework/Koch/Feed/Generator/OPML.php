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

use Koch\Feed\Generator;

/**
 * OPMLCreator is a FeedCreator that implements OPML 1.0.
 *
 * @see http://opml.scripting.com/spec
 */
class OPML extends Generator
{

    public function __construct($identifier = '')
    {
        parent::__construct($identifier);
        $this->encoding = "utf-8";
    }

    public function renderFeed()
    {
        $feed = "<?xml version=\"1.0\" encoding=\"" . $this->encoding . "\"?>\n";
        $feed.= $this->_createGeneratorComment();
        $feed.= $this->_createStylesheetReferences();
        $feed.= "<opml xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\n";
        $feed.= "<head>    \n";
        $feed.= "        <title>" . htmlspecialchars($this->title) . "</title>\n";
        if ($this->pubDate != "") {
            $date = new FeedDate($this->pubDate);
            $feed.= "         <dateCreated>" . $date->rfc822() . "</dateCreated>\n";
        }
        if ($this->lastBuildDate != "") {
            $date = new FeedDate($this->lastBuildDate);
            $feed.= "         <dateModified>" . $date->rfc822() . "</dateModified>\n";
        }
        if ($this->editor != "") {
            $feed.= "         <ownerName>" . $this->editor . "</ownerName>\n";
        }
        if ($this->editorEmail != "") {
            $feed.= "         <ownerEmail>" . $this->editorEmail . "</ownerEmail>\n";
        }
        $feed.= "    </head>\n";
        $feed.= "    <body>\n";
        for ($i = 0; $i < count($this->items); $i++) {
            $feed.= "    <outline type=\"rss\" ";
            $title = htmlspecialchars(strip_tags(strtr($this->items[$i]->title, "\n\r", "  ")));
            $feed.= " title=\"" . $title . "\"";
            $feed.= " text=\"" . $title . "\"";
            //$feed.= " description=\"".htmlspecialchars($this->items[$i]->description)."\"";
            $feed.= " url=\"" . htmlspecialchars($this->items[$i]->link) . "\"";
            $feed.= "/>\n";
        }
        $feed.= "    </body>\n";
        $feed.= "</opml>\n";

        return $feed;
    }
}
