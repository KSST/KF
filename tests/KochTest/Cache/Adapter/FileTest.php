<?php

namespace KochTest\Cache\Adapter;

use Koch\Cache\Adapter\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var File
     */
    protected $object;

    public function setUp()
    {
        $this->object = new File;
    }


    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Cache\Adapter\File::contains
     * @covers Koch\Cache\Adapter\File::store
     * @covers Koch\Cache\Adapter\File::fetch
     * @covers Koch\Cache\Adapter\File::delete
     */
    public function testFetch()
    {
        // key does not exist before
        $this->object->delete('key1');
        $this->assertFalse($this->object->contains('key1'));
        // not possible to add key with value without a TTL
        $this->assertFalse($this->object->store('key1', 'value1'));
        // add key with value
        $this->object->store('key1', 'value1', 120);
        // get that value by key
        $this->assertEquals('value1', $this->object->fetch('key1'));
        // just check if such a key is set
        $this->assertTrue($this->object->contains('key1'));
        // now delete the key
        $this->object->delete('key1');
        // check that it's gone
        $this->assertFalse($this->object->contains('key1'));
        // assert that, cache clearing works
        $this->assertTrue($this->object->clear());
    }

    /**
     * @covers Koch\Cache\Adapter\File::stats
     */
    public function testStats()
    {
        $this->assertTrue(is_array($this->object->stats()));
    }

    /**
     * @covers Koch\Cache\Adapter\File::stats
     */
    public function testCreateFilenameFromKey()
    {
       $key = 'ABC';
       $er = $this->object->createFilenameFromKey($key);
       // not testing the actual middle part (composed of dir/dir/filename)
       $this->assertContains(APPLICATION_CACHE_PATH, $er);
       $this->assertContains('.kf.cache', $er);
    }
}
