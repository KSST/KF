<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Subject Under Test
        $this->validator = new Text();
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

        $this->assertTrue($this->validator->validate('string'));

        $this->assertTrue($this->validator->validate(1));
        $this->assertTrue($this->validator->validate(1.01));

        $this->assertFalse($this->validator->validate(true));
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
