<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Range;

/**
 * @todo method chaining tests on all setter methods
 */
class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Range
     */
    protected $validator;

    public $options;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new Range();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->validator);
    }

    public function testMethodsetRange()
    {
        $minLength = '1';
        $maxLength = '1980';
        $this->validator->setRange($minLength, $maxLength);

        // string != int
        $this->assertNotSame($minLength, $this->validator->options['options']['min_range']);

        $this->assertNotSame($maxLength, $this->validator->options['options']['max_range']);

        // string to int
        $this->assertEquals($minLength, $this->validator->options['options']['min_range']);

        // string to int
        $this->assertEquals($maxLength, $this->validator->options['options']['max_range']);
    }

    public function testMethodprocessValidationLogic()
    {
        $minimum_length = '1';
        $maximum_length = '1980';
        $this->validator->setRange($minimum_length, $maximum_length);

        $this->assertFalse($this->validator->validate(''));
        $this->assertTrue($this->validator->validate(1));
        $this->assertTrue($this->validator->validate('1'));
        $this->assertTrue($this->validator->validate(true));
        $this->assertFalse($this->validator->validate(0));
        $this->assertFalse($this->validator->validate('0'));
        $this->assertFalse($this->validator->validate(false));

        // strings.. are not in range
        $this->assertFalse($this->validator->validate('Evolution'));
    }

    public function testMethodgetErrorMessage()
    {
        $minLength = '1';
        $maxLength = '1980';
        $this->validator->setRange($minLength, $maxLength);

        $this->assertSame('The value is outside the range of 1 <> 1980.',
                $this->validator->getErrorMessage());
    }

    public function testMethodgetValidationHint()
    {
        $minLength = '1';
        $maxLength = '1980';
        $this->validator->setRange($minLength, $maxLength);

        $this->assertSame('Please enter a value within the range of 1 <> 1980.',
                $this->validator->getValidationHint());
    }
}
