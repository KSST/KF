<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Equals;

class EqualsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Equals
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->validator = new Equals;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethod_getEqualsTo()
    {
        $this->validator->equalsTo = 1980;

        // getter returns integer
        $this->assertEquals(1980, $this->validator->getEqualsTo());

        // getter returns integer not string
        $this->assertNotSame('1980', $this->validator->getEqualsTo());
    }

    public function testMethod_setEqualsTo()
    {
         // setter accepts numeric
         $this->validator->setEqualsTo(19);
         $this->assertEquals(19, $this->validator->getEqualsTo());

         // setter accepts string
         $this->validator->setEqualsTo('19');
         $this->assertEquals(19, $this->validator->getEqualsTo());
    }

    public function testMethod_processValidationLogic()
    {
        // equals
        $this->validator->setEqualsTo('1980');
        $this->assertTrue($this->validator->validate('1980'));

        // !equals
        $this->validator->setEqualsTo('1980');
        $this->assertFalse($this->validator->validate('19801980'));
    }

    public function testMethod_getErrorMessage()
    {
        $this->assertTrue(is_string($this->validator->getErrorMessage()));
    }

    public function testMethod_getValidationHint()
    {
        $this->assertTrue(is_string($this->validator->getValidationHint()));
    }
}
