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
        unset($this->object);
    }

    /**
     * @covers Koch\Mail\SwiftMailer::send
     */
    public function testSend()
    {
        $from = 'kf-tests@trash-mail.com';
        $to = 'kf-tests@trash-mail.com';
        $subject = 'TestMail';
        $body = 'TestMail';
        $r = $this->object->send($to, $from, $subject, $body);

        $this->assertEquals("Sent 0 messages\n", $r);
    }

    /**
     * @covers Koch\Mail\SwiftMailer::getMailer
     */
    public function testGetMailer()
    {
        $r = $this->object->getMailer();
        $this->assertInstanceOf('Swift_Mailer', $r);
    }
}
