<?php

namespace KochTest\Logger\Adapter;

use Koch\Logger\Adapter\Firebug;

class FirebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Firebug
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Firebug;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Logger\Adapter\Firebug::getFirePHPLoglevel
     * @todo   Implement testGetFirePHPLoglevel().
     */
    public function testGetFirePHPLoglevel()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Logger\Adapter\Firebug::writeLog
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
