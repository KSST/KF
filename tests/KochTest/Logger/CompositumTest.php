<?php

namespace KochTest\Logger;

use Koch\Logger\Compositum;

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
    public function setUp()
    {
        $this->object = new Compositum;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Logger\Compositum::writeLog
     * @todo   Implement testWriteLog().
     */
    public function testWriteLog()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Logger\Compositum::addLogger
     * @todo   Implement testAddLogger().
     */
    public function testAddLogger()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Logger\Compositum::removeLogger
     * @todo   Implement testRemoveLogger().
     */
    public function testRemoveLogger()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
