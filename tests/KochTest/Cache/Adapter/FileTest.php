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

        @unlink($this->object->createFilenameFromKey('key1'));
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Cache\Adapter\File::__construct
     */
    public function testConstructor()
    {
        $options = array('key' => 'value');
        $this->object = new File($options);
    }

    /**
     * @covers Koch\Cache\Adapter\File::contains
     * @covers Koch\Cache\Adapter\File::store
     * @covers Koch\Cache\Adapter\File::fetch
     * @covers Koch\Cache\Adapter\File::delete
     * @covers Koch\Cache\Adapter\File::clear
     */
    public function testFetch()
    {
        // assert that, key does not exist before
        $this->assertFalse($this->object->delete('key1'));
        $this->assertFalse($this->object->contains('key1'));
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
     * @covers Koch\Cache\Adapter\File::stats
     */
    public function testStats()
    {
        // currently the array is empty
        $this->assertTrue(is_array($this->object->stats()));
    }

    /**
     * @covers Koch\Cache\Adapter\File::createFileNameFromKey
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
