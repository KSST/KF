<?php
namespace Koch\Mail;

use Koch\Config\Config;

class SwiftMailerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SwiftMailer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SwiftMailer(new Config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Koch\Mail\SwiftMailer::send
     */
    public function testSend()
    {

    }

    /**
     * @covers Koch\Mail\SwiftMailer::getMailer
     */
    public function testGetMailer()
    {

    }
}
