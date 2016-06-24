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
        $this->object = new Cache();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Cache\Cache::instantiate
     * @covers Koch\Autoload\Loader::autoload
     */
    public function testInstantiate()
    {
        $cache = Cache::instantiate('file');
        $this->assertTrue(is_object($cache));
    }

    /**
     * @covers Koch\Cache\Cache::store
     * @covers Koch\Cache\Cache::fetch
     * @covers Koch\Cache\Cache::contains
     * @covers Koch\Cache\Cache::delete
     */
    public function testContains()
    {
        $this->assertTrue(Cache::store('key1', 'value1'));
        $this->assertEquals('value1', Cache::fetch('key1'));
        $this->assertTrue(Cache::contains('key1'));
        $this->assertTrue(Cache::delete('key1'));
        $this->assertFalse(Cache::contains('key1'));
    }

    /**
     * @covers Koch\Cache\Cache::clear
     */
    public function testClear()
    {
        Cache::store('key1', 'value1');
        $this->assertTrue(Cache::clear());
        $this->assertFalse(Cache::contains('key1'));
    }

    /**
     * @covers Koch\Cache\Cache::storeObject
     * @covers Koch\Cache\Cache::fetchObject
     */
    public function testFetchObject()
    {
        // create obj
       $object        = new \stdClass();
        $object->key  = 'value';
        $object->key2 = 'value2';

       // store in cache
       $this->assertTrue(Cache::storeObject('stdClass', $object));

       // fetch and compare
       $this->assertEquals($object, Cache::fetchObject('stdClass'));
    }
}
