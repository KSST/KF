<?php
namespace KochTest\Feed;

use Koch\Feed\Feed;

class FeedTest extends \PHPUnit_Framework_TestCase
{
    // path to valid rss feed
    public $feed_url = '';

    public function setUp()
    {
        parent::setUp();

        require_once dirname(dirname(dirname(__DIR__))) . '/framework/Koch/Feed/Feed.php';

        // valid rss feed online source
        #$this->feed_url = 'http://groups.google.com/group/clansuite/feed/rss_v2_0_msgs.xml';
        $this->feed_url = __DIR__ . '/fixtures/clansuite_rss_v2_0_msgs.xml';#
    }

    public function tearDown()
    {
        $cachefile = __DIR__ . '/fixtures/' . md5($this->feed_url);

        if (is_file($cachefile)) {
            unlink($cachefile);
        }
    }

    /**
     * testMethod_fetchRSS()
     */
    public function testMethod_fetchRSS()
    {
        $simplepie_feed_object = Feed::fetchRSS($this->feed_url);

        $this->assertInternalType($simplepie_feed_object, 'SimplePie');
    }

    /**
     * testMethod_fetchRawRSS_withoutCaching()
     */
    public function testMethod_fetchRawRSS_withoutCaching()
    {
        $feedcontent = Feed::fetchRawRSS($this->feed_url, false);

        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);
    }

    /**
     * testMethod_fetchRawRSS_withCaching()
     */
    public function testMethod_fetchRawRSS_withCaching()
    {
        $feedcontent = Feed::fetchRawRSS($this->feed_url, true);

        // check for cache file
        $this->assertTrue(is_file(__DIR__ . '/fixtures/' .  md5($this->feed_url)));

        // check for content
        $this->assertContains('title>clansuite.com Google Group</title>', $feedcontent);
    }

    /**
     * testMethod_getFeedcreator()
     */
    public function testMethod_getFeedcreator()
    {
        $feedcreator_object = Feed::getFeedcreator();

        #$this->assertInternalType($feedcreator_object, 'FeedCreator');
        $this->assertInternalType($feedcreator_object, 'UniversalFeedCreator');
    }
}
