<?php
namespace KochTest\Config\Adapter;

use Koch\Config\Adapter\CSV;

class CSVTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CSV
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new CSV;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Config\Adapter\CSV::readConfig
     * @todo   Implement testReadConfig().
     */
    public function testReadConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Config\Adapter\CSV::writeConfig
     * @todo   Implement testWriteConfig().
     */
    public function testWriteConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
