<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\MinValue;

/**
 * @todo method chaining tests on all setter methods
 */
class MinValueTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MinValue
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new \Koch\Form\Validators\MinValue;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethodgetMinvalue()
    {
        // set property
        $this->validator->minvalue = 1980;

        // getter returns integer
        $this->assertEquals(1980, $this->validator->getMinvalue());

        // getter returns integer not string
        $this->assertNotSame('1980', $this->validator->getMinvalue());
    }

    /*
     * expectedException        InvalidArgumentException
     * expectedExceptionMessage Parameter Minvalue must be numeric (int|float) and not string.
     */

    public function testMethodsetMinvalue()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->validator->setMinvalue('1980');

        $this->validator->setMinvalue(1980);

        $this->assertEquals(1980, $this->validator->getMinvalue());
    }

    public function testMethodprocessValidationLogic()
    {
        /**
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */
        $value = 10; // 20 chars
        // int
        $this->validator->setMinvalue(10);
        $this->assertTrue($this->validator->validate($value));

        // float, too small
        $this->validator->setMinvalue(9.99);
        $this->assertTrue($this->validator->validate($value));

        // float, too big
        $this->validator->setMinvalue(10.01);
        $this->assertFalse($this->validator->validate($value));

        // int, too big
        $this->validator->setMinvalue(11);
        $this->assertFalse($this->validator->validate($value));
    }

    public function testMethodgetErrorMessage()
    {
        $this->validator->setMinvalue(19);

        $this->assertEquals('The value is less than the minimum value of 19.', $this->validator->getErrorMessage());
    }

    public function testMethodgetValidationHint()
    {
        $this->validator->setMinvalue(19);

        $this->assertEquals(
            'Please enter a value not being less than the minimum value of 19.',
            $this->validator->getValidationHint()
        );
    }

}
