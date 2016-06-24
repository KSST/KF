<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Required;

class RequiredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Required
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new \Koch\Form\Validators\Required();
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
        $this->assertFalse($this->validator->validate(''));

        $this->assertTrue($this->validator->validate('Evolution'));
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
