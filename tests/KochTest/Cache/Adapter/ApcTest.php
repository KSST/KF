<?php

namespace KochTest\Cache\Adapter;

use Koch\Cache\Adapter\Apc;

class ApcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Apc
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        if (!extension_loaded('apc')) {
            $this->markTestSkipped('The APC extension is not available.');
        }

        $this->object = new Apc;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Cache\Adapter\Apc::fetch
     * @todo   Implement testFetch().
     */
    public function testFetch()
    {
        $this->assertFalse($this->object->contains('key1'));
        $this->object->store('key1', 'value1');
        $this->assertEquals('value1', $this->object->fetch('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\Apc::store
     * @todo   Implement testStore().
     */
    public function testStore()
    {
        $this->assertFalse($this->object->contains('key1'));
        $this->object->store('key1', 'value1');
        $this->assertEquals('value1', $this->object->fetch('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\Apc::delete
     * @todo   Implement testDelete().
     */
    public function testDelete()
    {
        $this->assertFalse($this->object->contains('key1'));
        $this->object->store('key1', 'value1');
        $this->assertEquals('value1', $this->object->fetch('key1'));
        $this->assertTrue($this->object->contains('key1'));

        $this->object->delete('key1');

        $this->assertFalse($this->object->contains('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\Apc::clear
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
     * @covers Koch\Cache\Adapter\Apc::contains
     * @todo   Implement testContains().
     */
    public function testContains()
    {
        $this->assertFalse($this->object->contains('key1'));
        $this->object->store('key1', 'value1');
        $this->assertEquals('value1', $this->object->fetch('key1'));
        $this->assertTrue($this->object->contains('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\Apc::stats
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
     * @covers Koch\Cache\Adapter\Apc::compile_file
     * @todo   Implement testCompile_file().
     */
    public function testCompile_file()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\Apc::compile_dir
     * @todo   Implement testCompile_dir().
     */
    public function testCompile_dir()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
