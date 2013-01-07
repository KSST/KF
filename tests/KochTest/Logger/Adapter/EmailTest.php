<?php

namespace KochTest\Logger\Adapter;

use Koch\Logger\Adapter\Email;

class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Email
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // !class_exists('Swiftmailer') ?
        if (false === is_file(VENDOR_PATH . 'swiftmailer/swiftmailer/lib/swift_required.php')) {
            $this->markTestSkipped('This test requires the vendor library "Swiftmailer".');
        }

        $options['mail']['to_sysadmin'] = 'jakoch@web.de';
        $options['mail']['from'] = 'jakoch@web.de';

        $options['email']['method'] = 'mail';
        $options['email']['host'] = '';
        $options['email']['port'] = '';
        $options['email']['encryption'] = '';

        $options['date']['format'] = 'D.M.Y';

        $this->object = new Email($options);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Logger\Adapter\Email::writeLog
     */
    public function testWriteLog()
    {
        $data = array('message', 'label', 'priority');
        $r = $this->object->writeLog($data);
        $this->assertTrue($r);
    }
}
