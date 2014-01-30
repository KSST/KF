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
 * RSSCreator10 is a FeedCreator that implements RDF Site Summary (RSS) 1.0.
 *
 * @see http://cyber.law.harvard.edu/rss/rss.html
 */
class RSS10 extends Generator
{

    /**
     * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
     * The feed will contain all items previously added in the same order.
     *
     * @return string the feed's complete text
     */
    public function renderFeed()
    {
        $feed = "<?xml version=\"1.0\" encoding=\"" . $this->encoding . "\"?>\n";
        $feed.= $this->createGeneratorComment();
        if ($this->cssStyleSheet == "") {
            $cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
        }
        $feed.= $this->createStylesheetReferences();
        $feed.= "<rdf:RDF\n";
        $feed.= "    xmlns=\"http://purl.org/rss/1.0/\"\n";
        $feed.= "    xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n";
        $feed.= "    xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"\n";
        $feed.= "    xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
        $feed.= "    <channel rdf:about=\"" . $this->syndicationURL . "\">\n";
        $feed.= "        <title>" . htmlspecialchars($this->title) . "</title>\n";
        $feed.= "        <description>" . htmlspecialchars($this->description) . "</description>\n";
        $feed.= "        <link>" . $this->link . "</link>\n";
        if ($this->image != null) {
            $feed.= "        <image rdf:resource=\"" . $this->image->url . "\" />\n";
        }
        $now = new FeedDate();
        $feed.= "       <dc:date>" . htmlspecialchars($now->iso8601()) . "</dc:date>\n";
        $feed.= "        <items>\n";
        $feed.= "            <rdf:Seq>\n";
        for ($i = 0, $items = count($this->items); $i < $items; $i++) {
            $feed.= "                <rdf:li rdf:resource=\"" . htmlspecialchars($this->items[$i]->link) . "\"/>\n";
        }
        $feed.= "            </rdf:Seq>\n";
        $feed.= "        </items>\n";
        $feed.= "    </channel>\n";
        if ($this->image != null) {
            $feed.= "    <image rdf:about=\"" . $this->image->url . "\">\n";
            $feed.= "        <title>" . $this->image->title . "</title>\n";
            $feed.= "        <link>" . $this->image->link . "</link>\n";
            $feed.= "        <url>" . $this->image->url . "</url>\n";
            $feed.= "    </image>\n";
        }
        $feed.= $this->createAdditionalElements($this->additionalElements, "    ");

        for ($i = 0, $items = count($this->items); $i < $items; $i++) {
            $feed.= "    <item rdf:about=\"" . htmlspecialchars($this->items[$i]->link) . "\">\n";
            //$feed.= "        <dc:type>Posting</dc:type>\n";
            $feed.= "        <dc:format>text/html</dc:format>\n";
            if ($this->items[$i]->date != null) {
                $itemDate = new FeedDate($this->items[$i]->date);
                $feed.= "        <dc:date>" . htmlspecialchars($itemDate->iso8601()) . "</dc:date>\n";
            }
            if ($this->items[$i]->source != "") {
                $feed.= "        <dc:source>" . htmlspecialchars($this->items[$i]->source) . "</dc:source>\n";
            }
            if ($this->items[$i]->author != "") {
                $feed.= "        <dc:creator>" . htmlspecialchars($this->items[$i]->author) . "</dc:creator>\n";
            }
            $feed.= "        <title>" . htmlspecialchars(strip_tags(strtr($this->items[$i]->title, "\n\r", "  ")));
            $feed.= "</title>\n";
            $feed.= "        <link>" . htmlspecialchars($this->items[$i]->link) . "</link>\n";
            $feed.= "        <description>" . htmlspecialchars($this->items[$i]->description) . "</description>\n";
            $feed.= $this->createAdditionalElements($this->items[$i]->additionalElements, "        ");
            $feed.= "    </item>\n";
        }
        $feed.= "</rdf:RDF>\n";

        return $feed;
    }
}
