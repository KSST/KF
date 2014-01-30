<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Url
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new Url;
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
        /**
         * method processValidationLogic is indirectly tested via calling
         * validate() on the parent class, which then calls processValidationLogic()
         */

        // IDNA URL based on intl extension
        if (extension_loaded('intl') === true) {
            // converting works
            $this->assertEquals(idn_to_ascii('url-ästhetik.de'), 'xn--url-sthetik-o8a.de');

            // test punycode urls
            $this->assertFalse($this->validator->validate('url-ästhetik.de'));
            $this->assertTrue($this->validator->validate('http://url-ästhetik.de'));
            $this->assertTrue($this->validator->validate('http://www.täst.com'));
        }

        // no dash
        $this->assertTrue($this->validator->validate('http://application.com'));

        // 1 dash
        $this->assertTrue($this->validator->validate('http://app-lication.com'));

        // 2 dashes
        $this->assertTrue($this->validator->validate('http://jens-andre-koch.de'));
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
