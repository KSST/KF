<?php

namespace KochTest\Files;

use Koch\Files\Download;

class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Download
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Download;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Files\Download::getMimeType
     * @todo   Implement testGetMimeType().
     */
    public function testGetMimeType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Files\Download::send
     * @todo   Implement testSend().
     */
    public function testSend()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Files\Download::sendFile
     * @todo   Implement testSendFile().
     */
    public function testSendFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
