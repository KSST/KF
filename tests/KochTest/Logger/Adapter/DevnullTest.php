<?php

namespace KochTest\Logger\Adapter;

use Koch\Logger\Adapter\Devnull;

class DevnullTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Devnull
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Devnull;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Logger\Adapter\Devnull::writeLog
     * @todo   Implement testWriteLog().
     */
    public function testWriteLog()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
