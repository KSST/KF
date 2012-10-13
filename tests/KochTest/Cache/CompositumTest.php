<?php

namespace KochTest\Cache;

use Koch\Cache\Compositum;

class CompositumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compositum
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Compositum;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Cache\Compositum::addCache
     * @todo   Implement testAddCache().
     */
    public function testAddCache()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Compositum::removeCache
     * @todo   Implement testRemoveCache().
     */
    public function testRemoveCache()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Compositum::cache
     * @todo   Implement testCache().
     */
    public function testCache()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
