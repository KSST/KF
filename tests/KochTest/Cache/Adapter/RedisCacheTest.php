<?php

namespace KochTest\Cache\Adapter;

use Koch\Cache\Adapter\Redis;

class RedisCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisCache
     */
    protected $object;

    protected function setUp()
    {
        if (false === extension_loaded('redis')) {
            $this->markTestSkipped('The PHP extension "redis" is required.');
        }

        $this->object = new Redis;
    }

    protected function tearDown()
    {
        // close connection
        $this->object->redis->close();
        unset($object);
    }

    /**
     * @covers Koch\Cache\Adapter\Redis::contains
     * @covers Koch\Cache\Adapter\Redis::store
     * @covers Koch\Cache\Adapter\Redis::fetch
     * @covers Koch\Cache\Adapter\Redis::delete
     */
    public function testFetch()
    {
        // assert that, key does not exist before
        $this->assertFalse($this->object->delete('key1'));
        $this->assertFalse($this->object->contains('key1'));
        // assert that, it's not possible to add key with value without a TTL
        $this->assertFalse($this->object->store('key1', 'value1'));
        // assert that, we can add key with value with ttl
        $this->assertTrue($this->object->store('key1', 'value1', 120));
        // assert that, we can get that value by key
        $this->assertEquals('value1', $this->object->fetch('key1'));
        // assert that, we can check, if such a key is set
        $this->assertTrue($this->object->contains('key1'));
        // assert that, we can delete the key
        $this->assertTrue($this->object->delete('key1'));
        // assert that, we can check that the key is gone
        $this->assertFalse($this->object->contains('key1'));
        // assert that, clearing the whole cache works
        $this->assertTrue($this->object->clear());
    }

    /**
     * @covers Koch\Cache\Adapter\Redis::stats
     */
    public function testStats()
    {
        // currently the array is empty
        $this->assertTrue(is_array($this->object->stats()));
    }
}
