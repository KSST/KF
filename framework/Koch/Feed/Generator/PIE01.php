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
 * PIECreator01 is a FeedCreator that implements the emerging PIE specification,
 * as in http://intertwingly.net/wiki/pie/Syntax.
 */
class PIE01 extends Generator
{
    public function __construct($identifier = '')
    {
        parent::__construct($identifier);
        $this->encoding = "utf-8";
    }

    public function renderFeed()
    {
        $feed = "<?xml version=\"1.0\" encoding=\"" . $this->encoding . "\"?>\n";
        $feed.= $this->_createStylesheetReferences();
        $feed.= "<feed version=\"0.1\" xmlns=\"http://example.com/newformat#\">\n";
        $feed.= "    <title>" . FeedCreator::iTrunc(htmlspecialchars($this->title), 100) . "</title>\n";
        $this->truncSize = 500;
        $feed.= "    <subtitle>" . $this->getDescription() . "</subtitle>\n";
        $feed.= "    <link>" . $this->link . "</link>\n";
        for ($i = 0; $i < count($this->items); $i++) {
            $feed.= "    <entry>\n";
            $feed.= "        <title>" . FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)), 100) . "</title>\n";
            $feed.= "        <link>" . htmlspecialchars($this->items[$i]->link) . "</link>\n";
            $itemDate = new FeedDate($this->items[$i]->date);
            $feed.= "        <created>" . htmlspecialchars($itemDate->iso8601()) . "</created>\n";
            $feed.= "        <issued>" . htmlspecialchars($itemDate->iso8601()) . "</issued>\n";
            $feed.= "        <modified>" . htmlspecialchars($itemDate->iso8601()) . "</modified>\n";
            $feed.= "        <id>" . htmlspecialchars($this->items[$i]->guid) . "</id>\n";
            if ($this->items[$i]->author != "") {
                $feed.= "        <author>\n";
                $feed.= "            <name>" . htmlspecialchars($this->items[$i]->author) . "</name>\n";
                if ($this->items[$i]->authorEmail != "") {
                    $feed.= "            <email>" . $this->items[$i]->authorEmail . "</email>\n";
                }
                $feed.="        </author>\n";
            }
            $feed.= "        <content type=\"text/html\" xml:lang=\"en-us\">\n";
            $feed.= "            <div xmlns=\"http://www.w3.org/1999/xhtml\">" . $this->items[$i]->getDescription() . "</div>\n";
            $feed.= "        </content>\n";
            $feed.= "    </entry>\n";
        }
        $feed.= "</feed>\n";

        return $feed;
    }
}
