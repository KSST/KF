<?php

namespace KochTest\Logger\Adapter;

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
        // config is needed to fetch email of sysadmin
        //$config = new Config();

        //$this->object = new Email($config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Logger\Adapter\Email::writeLog
     * @todo   Implement testWriteLog().
     */
    public function testWriteLog()
    {
        $data = array('message', 'label', 'priority');
        $this->object->writeLog($data);
    }
}
