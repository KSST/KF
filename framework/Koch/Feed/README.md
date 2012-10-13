## FeedCreator

This is a fork of FeedCreator v1.7.2.
Originally (c) Kai Blankenhorn www.bitfolge.de - kaib@bitfolge.de
LGPL v2.1

v1.3        work by Scott Reynen (scott@randomchaos.com) and Kai Blankenhorn
v1.5        OPML support by Dirk Clemens
v1.7.2-mod  on-the-fly feed generation by Fabian Wolf (info@f2w.de)
v1.7.2-ppt  ATOM 1.0 support by Mohammad Hafiz bin Ismail (mypapit@gmail.com)

## Usage Example 

$rss = new FeedCreator();
$rss->useCached(); // use cached version if age<1 hour
$rss->title = "PHP news";
$rss->description = "daily news from the PHP scripting world";

//optional
$rss->descriptionTruncSize = 500;
$rss->descriptionHtmlSyndicated = true;

$rss->link = "http://www.dailyphp.net/news";
$rss->syndicationURL = "http://www.dailyphp.net/".$_SERVER['PHP_SELF'];

$image = new FeedImage();
$image->title = "dailyphp.net logo";
$image->url = "http://www.dailyphp.net/images/logo.gif";
$image->link = "http://www.dailyphp.net";
$image->description = "Feed provided by dailyphp.net. Click to visit.";

//optional
$image->descriptionTruncSize = 500;
$image->descriptionHtmlSyndicated = true;

$rss->image = $image;

// get your news items from somewhere, e.g. your database:
mysql_select_db($dbHost, $dbUser, $dbPass);
$res = mysql_query("SELECT * FROM news ORDER BY newsdate DESC");
while ($data = mysql_fetch_object($res)) {
    $item = new FeedItem();
    $item->title = $data->title;
    $item->link = $data->url;
    $item->description = $data->short;

    //optional
    item->descriptionTruncSize = 500;
    item->descriptionHtmlSyndicated = true;

    //optional (enclosure)
    $item->enclosure = new EnclosureItem();
    $item->enclosure->url='http://http://www.dailyphp.net/media/voice.mp3';
    $item->enclosure->length="950230";
    $item->enclosure->type='audio/x-mpeg'



    $item->date = $data->newsdate;
    $item->source = "http://www.dailyphp.net";
    $item->author = "John Doe";

    $rss->addItem($item);
}

// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
// MBOX, OPML, ATOM, ATOM10, ATOM0.3, HTML, JS
echo $rss->saveFeed("RSS1.0", "news/feed.xml");

//to generate "on-the-fly"
$rss->outputFeed("RSS1.0");