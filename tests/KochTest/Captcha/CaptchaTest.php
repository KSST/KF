<?php

namespace KochTest\Captcha;

use Koch\Captcha\Captcha;

class CaptchaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Captcha
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        if (extension_loaded('gd') === false) {
            $this->markTestSkipped('The GD extension is not available.');
        }

        $this->object = new Captcha;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    public function testSetFontFolder()
    {
        // accepts string
        $this->object->setFontFolder('folder');

        $expected_folders = $this->object->getFontFolders();
        // note:  $expected_folders[0] is the path to the framework's font folder
        $this->assertEquals('folder', $expected_folders[1]);

        // accepts array
        $folders = array('folder/A', 'folder/B');

        $this->object->setFontFolder($folders);

        $expected_folders = array_merge($expected_folders, $folders);
        $this->assertEquals($expected_folders, $this->object->getFontFolders());
    }

    /**
     * @covers Koch\Captcha\Captcha::getRandomFont
     * @todo   Implement testGetRandomFont().
     */
    public function testGetRandomFont()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Captcha\Captcha::generateRandomString
     */
    public function testGenerateRandomString()
    {
        $length = 5;
        $randomString = $this->object->generateRandomString($length);

        // test valid chars, length, excluded chars
        $constraint = $this->logicalAnd(
             $this->isType('string'),
             $this->matchesRegularExpression('/[a-zA-Z0-9]{5}/i'),
             $this->logicalNot(
                $this->matchesRegularExpression('/[017IO]/i')
            )
        );
        $this->assertThat($randomString, $constraint);

        // silly random test
        $this->assertNotEquals($randomString, $this->object->generateRandomString($length));
    }

    /**
     * @covers Koch\Captcha\Captcha::generateCaptchaImage
     * @todo   Implement testGenerateCaptchaImage().
     */
    public function testGenerateCaptchaImage()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Captcha\Captcha::render
     * @todo   Implement testRender().
     */
    public function testRender()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Captcha\Captcha::garbage_collection
     * @todo   Implement testGarbage_collection().
     */
    public function testGarbage_collection()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
