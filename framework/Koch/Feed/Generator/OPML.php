<?php

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

	public function createFeed()
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