<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Ip;

class IpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Ip
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new Ip();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethodprocessValidationLogic()
    {
        /*
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */

        // ipv4 - num
        $this->assertTrue($this->validator->validate('127.0.0.1'));

        // ipv4 - num false
        $this->assertFalse($this->validator->validate('127.0.0.1.127'));

        $ipv6 = '2001:0db8:85a3:08d3:1319:8a2e:0370:7344';
        $this->assertTrue($this->validator->validate($ipv6));

        // does not accept URLs -> use URL Validator
        $this->assertFalse($this->validator->validate('application.com'));
    }

    public function testMethodgetErrorMessage()
    {
        $this->assertTrue(is_string($this->validator->getErrorMessage()));
    }

    public function testMethodgetValidationHint()
    {
        $this->assertTrue(is_string($this->validator->getValidationHint()));
    }
}
