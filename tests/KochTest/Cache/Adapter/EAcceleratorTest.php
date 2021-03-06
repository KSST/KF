<?php

namespace KochTest\Cache\Adapter;

use Koch\Cache\Adapter\EAccelerator;

class EAcceleratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EAccelerator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        if (!extension_loaded('eaccelerator')) {
            $this->markTestSkipped('This test requires the PHP extension "eaccelerator".');
        }

        $this->object = new EAccelerator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Cache\Adapter\EAccelerator::contains
     *
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
     * @covers Koch\Cache\Adapter\EAccelerator::fetch
     *
     * @todo   Implement testFetch().
     */
    public function testFetch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\EAccelerator::store
     *
     * @todo   Implement testStore().
     */
    public function testStore()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Cache\Adapter\EAccelerator::delete
     *
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
     * @covers Koch\Cache\Adapter\EAccelerator::clear
     *
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
     * @covers Koch\Cache\Adapter\EAccelerator::stats
     *
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
