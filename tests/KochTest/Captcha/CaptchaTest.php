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
            $this->markTestSkipped('This test requires the PHP extension "gd".');
        }

        // set captcha folder
        $options['captcha_dir'] = __DIR__;

        $this->object = new Captcha($options);
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
     */
    public function testGetRandomFont()
    {
       $font = $this->object->getRandomFont();

       $this->assertContains('.ttf', $font);
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
                $this->matchesRegularExpression('/[017IO]/') // not case-[i]nsensitve
            )
        );
        $this->assertThat($randomString, $constraint);

        // silly random test
        $this->assertNotEquals($randomString, $this->object->generateRandomString($length));
    }

    /**
     * @covers Koch\Captcha\Captcha::generateCaptchaImage
     */
    public function testGenerateCaptchaImage()
    {
       $font = $this->object->getRandomFont();
       $this->object->setFont($font);

       // base64 embedded captcha image
       $result = $this->object->generateCaptchaImage();
       $this->assertContains(
           '<img alt="Embedded Captcha Image" src="data:image/png;base64,',
           $result
       );

    }

    /**
     * @covers Koch\Captcha\Captcha::render
     */
    public function testRender()
    {
        // lets generate a simple image
        $image = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($image, 233, 14, 91);
        imagestring($image, 1, 5, 5, 'A Text String', $text_color);

        // lets pretend that this is the generated captcha
        $this->object->captcha = $image;

        // now test output methods

        /* @todo
        $render_type = 'file';
        $this->object->render($render_type);
        */

        $render_type = 'base64';
        $result = $this->object->render($render_type);
        $this->assertContains(
           '<img alt="Embedded Captcha Image" src="data:image/png;base64,',
           $result
        );

        /* @todo
        $render_type = 'png';
        $this->object->render($render_type);
        */
    }

    /**
     * @covers Koch\Captcha\Captcha::collectGarbage
     */
    public function testCollectGarbage()
    {
       $this->assertTrue($this->object->collectGarbage());
    }
}
