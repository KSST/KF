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
 * RSSCreator091 is a FeedCreator that implements RSS 0.91 Spec, revision 3.
 *
  * @see http://cyber.law.harvard.edu/rss/rss.html
 */
class RSS091 extends Generator
{
    /**
     * Stores this RSS feed's version number.
     */
    protected $rssVersion = '0.91';

    public function __construct($identifier = '')
    {
        parent::__construct($identifier);

        $this->contentType = 'application/rss+xml';
    }

    /**
     * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
     * The feed will contain all items previously added in the same order.
     * @return string the feed's complete text
     */
    public function renderFeed()
    {
        $feed = "<?xml version=\"1.0\" encoding=\"" . $this->encoding . "\"?>\n";
        $feed.= $this->createGeneratorComment();
        $feed.= $this->createStylesheetReferences();
        $feed.= "<rss version=\"" . $this->rssVersion . "\">\n";
        $feed.= "    <channel>\n";
        $feed.= '        <title>' . FeedCreator::iTrunc(htmlspecialchars($this->title), 100) . "</title>\n";
        $this->descriptionTruncSize = 500;
        $feed.= '        <description>' . $this->getDescription() . "</description>\n";
        $feed.= '        <link>' . $this->link . "</link>\n";
        if ('2.0' == substr($this->rssVersion, 0, 3) AND !empty($this->syndicationURL)) {
            $feed .= "    <atom:link href=\"$this->syndicationURL\" rel=\"self\" type=\"application/rss+xml\" />";
        }
        $now = new FeedDate();
        $feed.= '        <lastBuildDate>' . htmlspecialchars($now->rfc822()) . "</lastBuildDate>\n";
        $feed.= '        <generator>' . FEEDGENERATOR_VERSION . "</generator>\n";

        if ($this->image != null) {
            $feed.= "        <image>\n";
            $feed.= '            <url>' . $this->image->url . "</url>\n";
            $feed.= '            <title>';
            $feed.= FeedCreator::iTrunc(htmlspecialchars($this->image->title), 100) . "</title>\n";
            $feed.= '            <link>' . $this->image->link . "</link>\n";
            if ($this->image->width != '') {
                $feed.= '            <width>' . $this->image->width . "</width>\n";
            }
            if ($this->image->height != '') {
                $feed.= '            <height>' . $this->image->height . "</height>\n";
            }
            if ($this->image->description != '') {
                $feed.= '            <description>' . $this->image->getDescription() . "</description>\n";
            }
            $feed.= "        </image>\n";
        }
        if ($this->language != '') {
            $feed.= '        <language>' . $this->language . "</language>\n";
        }
        if ($this->copyright != '') {
            $feed.= '        <copyright>';
            $feed.= FeedCreator::iTrunc(htmlspecialchars($this->copyright), 100) . "</copyright>\n";
        }
        if ($this->editor != '') {
            $feed.= '        <managingEditor>';
            $feed.= FeedCreator::iTrunc(htmlspecialchars($this->editor), 100) . "</managingEditor>\n";
        }
        if ($this->webmaster != '') {
            $feed.= '        <webMaster>';
            $feed.= FeedCreator::iTrunc(htmlspecialchars($this->webmaster), 100) . "</webMaster>\n";
        }
        if ($this->pubDate != '') {
            $pubDate = new FeedDate($this->pubDate);
            $feed.= '        <pubDate>' . htmlspecialchars($pubDate->rfc822()) . "</pubDate>\n";
        }
        if ($this->category != '') {
            $feed.= '        <category>' . htmlspecialchars($this->category) . "</category>\n";
        }
        if ($this->docs != '') {
            $feed.= '        <docs>' . FeedCreator::iTrunc(htmlspecialchars($this->docs), 500) . "</docs>\n";
        }
        if ($this->ttl != '') {
            $feed.= '        <ttl>' . htmlspecialchars($this->ttl) . "</ttl>\n";
        }
        if ($this->rating != '') {
            $feed.= '        <rating>' . FeedCreator::iTrunc(htmlspecialchars($this->rating), 500) . "</rating>\n";
        }
        if ($this->skipHours != '') {
            $feed.= '        <skipHours>' . htmlspecialchars($this->skipHours) . "</skipHours>\n";
        }
        if ($this->skipDays != '') {
            $feed.= '        <skipDays>' . htmlspecialchars($this->skipDays) . "</skipDays>\n";
        }
        $feed.= $this->createAdditionalElements($this->additionalElements, "    ");

        for ($i = 0; $i < count($this->items); $i++) {
            $feed.= "        <item>\n";
            $feed.= "            <title>";
            $feed.= FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)), 100) . "</title>\n";
            $feed.= "            <link>" . htmlspecialchars($this->items[$i]->link) . "</link>\n";
            $feed.= "            <description>" . $this->items[$i]->getDescription() . "</description>\n";

            if ($this->items[$i]->author != '') {
                $feed.= "            <author>" . htmlspecialchars($this->items[$i]->author) . "</author>\n";
            }
            /*
              // on hold
              if ($this->items[$i]->source!="") {
              $feed.= "            <source>".htmlspecialchars($this->items[$i]->source)."</source>\n";
              }
             */
            if ($this->items[$i]->category != '') {
                $feed.= '            <category>' . htmlspecialchars($this->items[$i]->category) . "</category>\n";
            }
            if ($this->items[$i]->comments != '') {
                $feed.= '            <comments>' . htmlspecialchars($this->items[$i]->comments) . "</comments>\n";
            }
            if ($this->items[$i]->date != '') {
                $itemDate = new FeedDate($this->items[$i]->date);
                $feed.= '            <pubDate>' . htmlspecialchars($itemDate->rfc822()) . "</pubDate>\n";
            }
            if ($this->items[$i]->guid != '') {
                $feed.= '            <guid>' . htmlspecialchars($this->items[$i]->guid) . "</guid>\n";
            }
            $feed.= $this->createAdditionalElements($this->items[$i]->additionalElements, "        ");
            if ('2.0' == substr($this->rssVersion, 0, 3)
                AND !empty($this->syndicationURL) AND $this->items[$i]->enclosure != null) {
                $feed.= '            <enclosure url="';
                $feed.= $this->items[$i]->enclosure->url;
                $feed.= '" length="';
                $feed.= $this->items[$i]->enclosure->length;
                $feed.= '" type="';
                $feed.= $this->items[$i]->enclosure->type;
                $feed.= '" />' . "\n";
            }
            $feed.= "        </item>\n";
        }
        $feed.= "    </channel>\n";
        $feed.= "</rss>\n";

        return $feed;
    }
}
