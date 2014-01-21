<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\MaxValue;

class MaxValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MaxValue
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new \Koch\Form\Validators\MaxValue;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethodgetMaxvalue()
    {
        $this->validator->maxvalue = 1980;

        // getter returns integer
        $this->assertEquals(1980, $this->validator->getMaxvalue());

        // getter returns integer not string
        $this->assertNotSame('1980', $this->validator->getMaxvalue());
    }

    /*
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Parameter Maxvalue must be numeric (int|float) and not string.
     */
    public function testMethodsetMaxvalue()
    {
         $this->setExpectedException('InvalidArgumentException');
         $this->validator->setMaxvalue('19');
         $this->assertEquals(19, $this->validator->getMaxvalue());

         // setter accepts numeric
         $this->validator->setMaxvalue(19);
         $this->assertEquals(19, $this->validator->getMaxvalue());
    }

    /*
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    Parameter Maxvalue must be numeric (int|float) and not string.
     */
    public function testMethodprocessValidationLogic()
    {
        /**
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */

        $this->validator->setMaxvalue(19);
        $this->assertTrue($this->validator->validate(19));

        $this->validator->setMaxvalue(19);
        $this->assertFalse($this->validator->validate(20));

        $this->validator->setMaxvalue(19);
        $this->assertTrue($this->validator->validate(0));

        $this->setExpectedException('InvalidArgumentException');
        $this->validator->setMaxvalue('19');
        $this->assertTrue($this->validator->validate(19));

        $this->validator->setMaxvalue(20);
        $this->assertTrue($this->validator->validate(20));

        $this->validator->setMaxvalue(21);
        $this->assertTrue($this->validator->validate(21));
    }

    public function testMethodgetErrorMessage()
    {
        $this->validator->setMaxvalue(1980);

        $this->assertEquals('The value exceeds the maximum value of 1980.',
                           $this->validator->getErrorMessage());;
    }

    public function testMethodgetValidationHint()
    {
        $this->validator->setmaxvalue(1980);

        $this->assertEquals('The value must be smaller than 1980.',
                           $this->validator->getValidationHint());;
    }
}
