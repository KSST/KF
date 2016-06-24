<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\MaxLength;

class MaxLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Maxlength
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new MaxLength();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethodgetMaxlength()
    {
        $this->validator->maxlength = 1980;

        // getter returns integer
        $this->assertEquals(1980, $this->validator->getMaxlength());

        // getter returns integer not string
        $this->assertNotSame('1980', $this->validator->getMaxlength());
    }

    public function testMethodsetMaxlength()
    {
        // setter accepts numeric
        $this->validator->setMaxlength(19);
        $this->assertEquals(19, $this->validator->getMaxlength());

        // setter accepts string
        $this->validator->setMaxlength('19');
        $this->assertEquals(19, $this->validator->getMaxlength());
    }

    public function testMethodgetStringLength_mbstring()
    {
        if (!function_exists('mb_strlen')) {
            Koch\Localization\Utf8::initialize();
        }
        $this->assertEquals(36, $this->validator->getStringLength('äöü-öäü-äöü-german-umlauts-ûúéáóâôéê'));
    }

    public function testMethodprocessValidationLogic()
    {
        /*
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */
        $value = '12345678901234567890'; // 20 chars

        $this->validator->setMaxlength('19');
        $this->assertFalse($this->validator->validate($value));

        $this->validator->setMaxlength('20');
        $this->assertTrue($this->validator->validate($value));

        $this->validator->setMaxlength('21');
        $this->assertTrue($this->validator->validate($value));

        $value = ''; // 0 chars
        $this->validator->setMaxlength('0');
        $this->assertTrue($this->validator->validate($value));
    }

    public function testMethodgetErrorMessage()
    {
        $this->validator->setMaxlength('1980');

        $this->assertEquals('The value exceeds the maxlength of 1980 chars', $this->validator->getErrorMessage());
    }

    public function testMethodgetValidationHint()
    {
        $this->validator->setMaxlength('1980');

        $this->assertEquals('Please enter 1980 chars at maximum.', $this->validator->getValidationHint());
    }
}
