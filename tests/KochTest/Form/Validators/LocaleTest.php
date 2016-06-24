<?php

namespace KochTest\Form\Validators;

use Koch\Form\Validators\Locale;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Locale
     */
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // Test Subject
        $this->validator = new Locale();
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

        $this->assertTrue($this->validator->validate('de'));

        $this->assertTrue($this->validator->validate('de_DE'));

        // accepts "minus" instead of underscore as sublocale separator
        $this->assertTrue($this->validator->validate('de-DE'));

        // accepts "minus" and "lowercased sublocale"
        $this->assertTrue($this->validator->validate('de-de'));

        // zh-TW - Chinese Taiwan
        $this->assertTrue($this->validator->validate('zh-TW')); // zh-Hant-TW

        // fr-CA Canadian Frenchy
        $this->assertTrue($this->validator->validate('fr-CA'));

        $this->assertTrue($this->validator->validate('en-US'));

        $this->assertTrue($this->validator->validate('no-No'));

        $this->assertFalse($this->validator->validate('yy_non-existing-locale'));
    }

    public function testMethodisLocaleWithInvalidLocale()
    {
        // locale does not exist
        $this->assertFalse($this->validator->isLocale('yy_non-existing-locale'));

        // locale "de" exists, sublocale "de_XX" not
        $this->assertFalse($this->validator->isLocale('de_XX'));

        // locale "de" exists, accepts lowercased sublocale "de", normally this must be "de_DE"
        $this->assertTrue($this->validator->isLocale('de_de'));
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
