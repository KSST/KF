<?php

namespace KochTest\Cache\Adapter;

use Koch\Cache\Adapter\Memcached;

class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Memcached
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('The memcached extension is not available.');
        }

        $this->object = new Memcached;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::contains
     * @todo   Implement testContains().
     */
    public function testContains()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::get
     * @todo   Implement testGet().
     */
    public function testGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::fetch
     * @todo   Implement testFetch().
     */
    public function testFetch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::set
     * @todo   Implement testSet().
     */
    public function testSet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::store
     * @todo   Implement testStore().
     */
    public function testStore()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::delete
     * @todo   Implement testDelete().
     */
    public function testDelete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::clear
     * @todo   Implement testClear().
     */
    public function testClear()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::stats
     * @todo   Implement testStats().
     */
    public function testStats()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::getEngine
     * @todo   Implement testGetEngine().
     */
    public function testGetEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::__destruct
     * @todo   Implement test__destruct().
     */
    public function test__destruct()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
