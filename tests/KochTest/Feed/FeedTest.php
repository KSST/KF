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

        require_once dirname(dirname(dirname(__DIR__))) . '/framework/Koch/Feed/Feed.php';

        // valid rss feed online source
        #$this->feed_url = 'http://groups.google.com/group/clansuite/feed/rss_v2_0_msgs.xml';
        $this->feedUrl = __DIR__ . '/fixtures/clansuite_rss_v2_0_msgs.xml';#

        $this->cachedFile = __DIR__ . '/fixtures/' . md5($this->feedUrl);
    }

    /**
     * testMethod_fetchRSS()
     */
    public function testMethod_fetchRSS()
    {
        $this->markTestIncomplete('Test fails with exit code 255');
        //$simplepie_feed_object = Feed::fetchRSS($this->feed_url);

        //$this->assertInternalType($simplepie_feed_object, 'SimplePie');
    }

    /**
     * testMethod_fetchRawRSS_withoutCaching()
     */
    public function testMethod_fetchRawRSS_withoutCaching()
    {
        $feedcontent = Feed::fetchRawRSS($this->feedUrl, false);

        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);
    }

    /**
     * testMethod_fetchRawRSS_withCaching()
     */
    public function testMethod_fetchRawRSS_withCaching()
    {
        $feedcontent = Feed::fetchRawRSS($this->feedUrl, true);

        // check for cache file
        $this->assertTrue($this->cacheFile);

        // check for content
        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);

        if (is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    /**
     * testMethod_getFeedcreator()
     */
    public function testMethod_getFeedcreator()
    {
        $this->markTestIncomplete('Feedcreator not yet in vendors');
        //$feedcreator_object = Feed::getFeedcreator();
        //$this->assertInternalType($feedcreator_object, 'FeedCreator');
        //$this->assertInstanceOf($feedcreator_object, 'UniversalFeedCreator');
    }
}
