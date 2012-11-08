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

    public function tearDown()
    {
        $this->object = null;
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::contains
     * @covers Koch\Cache\Adapter\Memcached::store
     * @covers Koch\Cache\Adapter\Memcached::fetch
     * @covers Koch\Cache\Adapter\Memcached::delete
     */
    public function testContains()
    {
        // key does not exist before
        $this->assertFalse($this->object->contains('key1'));
        // add key with value
        $this->object->store('key1', 'value1');
        // get that value by key
        $this->assertEquals('value1', $this->object->fetch('key1'));
        // just check if such a key is set
        $this->assertTrue($this->object->contains('key1'));
        // now delete the key
        $this->object->delete('key1');
        // check that it's gone
        $this->assertFalse($this->object->contains('key1'));
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
