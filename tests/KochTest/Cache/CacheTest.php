<?php

namespace KochTest\Cache;

use Koch\Cache\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Cache;
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Cache\Cache::instantiate
     */
    public function testInstantiate()
    {
        $cache = Cache::instantiate('file');
        $this->assertTrue(is_object($cache));
    }

    /**
     * @covers Koch\Cache\Cache::contains
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
     * @covers Koch\Cache\Cache::store
     * @covers Koch\Cache\Cache::fetch
     */
    public function testStore()
    {
        Cache::store('key1', 'value1');
        $er = Cache::fetch('key1');
        $this->assertEquals('value1', $er);
    }

    /**
     * @covers Koch\Cache\Cache::delete
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
     * @covers Koch\Cache\Cache::clear
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
     * @covers Koch\Cache\Cache::fetchObject
     * @todo   Implement testFetchObject().
     */
    public function testFetchObject()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Cache::storeObject
     * @todo   Implement testStoreObject().
     */
    public function testStoreObject()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
