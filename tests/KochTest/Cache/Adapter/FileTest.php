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
        // assert that, cache is reset - nothing to delete
        $this->assertFalse($this->object->clear());
        // key does not exist before
        var_dump($this->object->contains('key1'));
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
        // assert that, cache clearing works
        $this->assertTrue($this->object->clear());
    }

    /**
     * @covers Koch\Cache\Adapter\File::stats
     * @todo   Implement testStats().
     */
    public function testStats()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
