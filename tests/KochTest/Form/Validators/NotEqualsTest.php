<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\NotEquals;

class NotEqualsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotEquals
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new NotEquals();
    }

    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethodgetNotEqualsTo()
    {
        $this->validator->notEqualsTo = 1980;

        // getter returns integer
        $this->assertNotEquals(1981, $this->validator->getNotEqualsTo());

        // getter returns integer not string
        $this->assertNotSame('1981', $this->validator->getNotEqualsTo());
    }

    public function testMethodsetNotEqualsTo()
    {
        // setter accepts numeric
         $this->validator->setNotEqualsTo(19);
        $this->assertNotEquals(18, $this->validator->getNotEqualsTo());

         // setter accepts string
         $this->validator->setNotEqualsTo('19');
        $this->assertNotEquals(18, $this->validator->getNotEqualsTo());
    }

    public function testMethodprocessValidationLogic()
    {
        // NotEquals
        $this->validator->setNotEqualsTo('1981');
        $this->assertTrue($this->validator->validate('1980'));

        // Equals
        $this->validator->setNotEqualsTo('1980');
        $this->assertFalse($this->validator->validate('1980'));
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
