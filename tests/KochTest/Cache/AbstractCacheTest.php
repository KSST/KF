<?php

namespace KochTest\Cache;

use Koch\Cache\AbstractCache;

class AbstractCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCache
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // cannot instantiate abstract class
        // $this->object = new AbstractCache;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Cache\AbstractCache::setPrefix
     * @todo   Implement testSetPrefix().
     */
    public function testSetPrefix()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\AbstractCache::getPrefix
     * @todo   Implement testGetPrefix().
     */
    public function testGetPrefix()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\AbstractCache::applyPrefix
     * @todo   Implement testApplyPrefix().
     */
    public function testApplyPrefix()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
