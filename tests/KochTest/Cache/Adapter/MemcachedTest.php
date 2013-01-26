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
            $this->markTestSkipped('This test requires the PHP extension "memcached".');
        }

        $this->object = new Memcached;
    }

    public function tearDown()
    {
        $this->object = null;
    }


    public static function SetOptionDataprovider()
    {
        return array(
          array('useConnection', 'default'),
          array('connection', array('default' => array()))
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::setOption
     * @dataProvider SetOptionDataprovider
     */
    public function testSetOption($key, $value)
    {
        $this->object->setOption($key, $value);
        $this->assertEquals($this->object->options[$key], $value);
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
        $this->assertTrue($this->object->store('key1', 'value1'));
        // get that value by key
        $this->assertEquals('value1', $this->object->fetch('key1'));
        // just check if such a key is set
        $this->assertTrue($this->object->contains('key1'));
        // now delete the key
        $this->assertTrue($this->object->delete('key1'));
        // check that it's gone
        $this->assertFalse($this->object->contains('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::clear
     */
    public function testClear()
    {
        // check that key not exists before
        $this->assertFalse($this->object->contains('key1'));
        // add key with value
        $this->assertTrue($this->object->store('key1', 'value1'));
        // just check if such a key is set
        $this->assertTrue($this->object->contains('key1'));

        $this->assertTrue($this->object->clear());

         // check that it's gone
        $this->assertFalse($this->object->contains('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\Memcached::stats
     * @todo   Implement testStats().
     */
    public function testStats()
    {
        $this->assertTrue(is_array($this->object->stats()));
    }
}
