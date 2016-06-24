<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Email;

class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Email
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new Email();
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
     * @return array
     */
    public function isValidEMailDataprovider()
    {
        return [
            //incomplete
            ['foo', false],
            ['äää', false],
            ['@', false],
            //umlaut in topleveldomain
            //array('test@test.öü', false), // @todo this is valid? punycode?
            //invalid character in name
            //array('foo/dsd@bar.de', false), // @todo this validates? with slash in name?
            //own toplevel domain, but valid
            ['max.mustermann@company.intranet', true],
            // umlauts in name
            ['karl.müller@123test.de', false], // @todo this is valid?
            // perfect
            ['charles@bronson.com', true],
            //long domainname with subdomains
            #array('herber-müller@servers.campus.univercity.edu', true), // @todo this does not validate.. why?
            // brackets in string
            ['billy[at]microsoft[dot]com', false],
        ];
    }

    /**
     * @dataProvider isValidEMailDataprovider
     * @covers Koch\Form\Validators\Email::processValidationLogic
     * @covers Koch\Form\Validator::processValidationLogic
     */
    public function testMethodprocessValidationLogic($email, $expectedValidationState)
    {
        /*
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */
        $this->assertEquals($expectedValidationState, $this->validator->validate($email));
    }

    /**
     * @covers Koch\Form\Validators\Email::getErrorMessage
     * @covers Koch\Form\Validator::getErrorMessage
     */
    public function testMethodgetErrorMessage()
    {
        $this->assertTrue(is_string($this->validator->getErrorMessage()));
    }

    /**
     * @covers Koch\Form\Validators\Email::getValidationHint
     * @covers Koch\Form\Validator::getValidationHint
     */
    public function testMethodgetValidationHint()
    {
        $this->assertTrue(is_string($this->validator->getValidationHint()));
    }
}
