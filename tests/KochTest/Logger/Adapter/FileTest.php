<?php

namespace KochTest\Logger\Adapter;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var File
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // config is needed for log rotation setting
        //$config = new Config();

       // $this->object = new File($config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Logger\Adapter\File::writeLog
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
     * @covers Koch\Logger\Adapter\File::readLog
     * @todo   Implement testReadLog().
     */
    public function testReadLog()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Logger\Adapter\File::getErrorLogFilename
     * @todo   Implement testGetErrorLogFilename().
     */
    public function testGetErrorLogFilename()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Logger\Adapter\File::returnEntriesFromLogfile
     * @todo   Implement testReturnEntriesFromLogfile().
     */
    public function testReturnEntriesFromLogfile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
