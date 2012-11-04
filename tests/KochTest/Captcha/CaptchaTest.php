<?php

namespace KochTest\Captcha;

use Koch\Captcha\Captcha;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-12 at 21:52:53.
 */
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
        $folders = 'folder';
        $this->object->setFontFolder($folders);

        $this->assertEquals($folders, $this->object->font_folders);

        // accepts array
        $folders = array('folder/A', 'folder/B');
        $this->object->setFontFolder($folders);

        $this->assertEquals($folders, $this->object->font_folders);
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

        // test that excluded chars are never in string
        $this->assertNotContains(array('0', '1', '7', 'I', 'O'), $randomString);

        // test length
        $this->assertCount($length, $randomString);

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
