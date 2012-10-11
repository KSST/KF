<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\RegExp;

class RegExpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegExp
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new RegExp;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    /**
     * @covers Koch\Form\Validators\RegExp::setRegexp
     * @covers Koch\Form\Validators\RegExp::getRegexp
     */
    public function testMethod_setRegexp()
    {
        $regexp = '/php/i';
        $this->validator->setRegexp($regexp);

        $this->assertEquals($this->validator->getRegexp(), $regexp);
    }

    /**
     * @covers Koch\Form\Validators\RegExp::setRegexp
     * @covers Koch\Form\Validators\RegExp::validate
     * @covers Koch\Form\Validator::processValidationLogic
     */
    public function testMethod_processValidationLogic()
    {
        /**
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */
        $regexp = '/php/i';
        $this->validator->setRegexp($regexp);
        $this->assertTrue($this->validator->validate('php is elefantastic.'));
    }

    /**
     * @covers Koch\Form\Validators\RegExp::getErrorMessage
     */
    public function testMethod_getErrorMessage()
    {
        $this->assertTrue(is_string($this->validator->getErrorMessage()));
    }

    /**
     * @covers Koch\Form\Validators\RegExp::getValidationHint
     */
    public function testMethod_getValidationHint()
    {
        $this->assertTrue(is_string($this->validator->getValidationHint()));
    }
}
