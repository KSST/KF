<?php
namespace KochTest\Feed;

use Koch\Feed\Feed;

class FeedTest extends \PHPUnit_Framework_TestCase
{
    // path to valid rss feed
    public $feedUrl = '';

    public $cacheFile = '';

    public function setUp()
    {
        parent::setUp();

        // valid rss feed online source
        #$this->feed_url = 'http://groups.google.com/group/clansuite/feed/rss_v2_0_msgs.xml';
        $this->feedUrl = __DIR__ . '/fixtures/clansuite_rss_v2_0_msgs.xml';#

        $this->cacheFolder = __DIR__ . '/fixtures/';
        $this->cacheFile = $this->cacheFolder . md5($this->feedUrl);
    }

    /**
     * testMethodfetchRSS()
     */
    public function testMethodfetchRSS()
    {
        $simplepie_feed_object = Feed::fetchRSS($this->feedUrl);

        $this->assertInstanceOf('SimplePie', $simplepie_feed_object);
    }

    /**
     * testMethodfetchRawRSS_withoutCaching()
     */
    public function testMethodfetchRawRSS_withoutCaching()
    {
        // read feed
        $feedcontent = Feed::fetchRawRSS($this->feedUrl, false);
        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);

        // return null, if feed not found
        $feedcontent = Feed::fetchRawRSS('non-existant-feed-URL', false);
        $this->assertNull($feedcontent);
    }

    /**
     * testMethodfetchRawRSS_withCaching()
     */
    public function testMethodfetchRawRSS_withCaching()
    {
        // fetch and cache
        $feedcontent = Feed::fetchRawRSS($this->feedUrl, true, $this->cacheFolder);
        // check for cache file
        $this->assertFileExists($this->cacheFile);
        // check for content
        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);

        // fetch from cache
        $feedcontent = Feed::fetchRawRSS($this->feedUrl, true, $this->cacheFolder);
        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);

        if (is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        // re-create cache - fetch and cache
        $feedcontent = Feed::fetchRawRSS($this->feedUrl, true, $this->cacheFolder);
        // check for cache file
        $this->assertFileExists($this->cacheFile);
    }
}
