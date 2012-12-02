<?php

namespace KochTest\Http\HttpResponse;

use Koch\Http\HttpResponse;

class HttpResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->response = new HttpResponse;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->response);
    }

    public function testProperty_DefaultStatusIs200()
    {
        $this->assertEquals(200, HttpResponse::getStatusCode());
    }

    /**
     * @covers HttpResponse::setStatusCode
     * @covers HttpResponse::getStatusCode
     */
    public function testSetAndGetStatusCode()
    {
        $code = '200';
        HttpResponse::setStatusCode($code);
        $this->assertEquals($code, HttpResponse::getStatusCode());
    }

    public function testGetStatusCodeDescription()
    {
        $code = '200';
        $this->assertEquals('OK', HttpResponse::getStatusCodeDescription($code));
    }

    /**
     * @covers HttpResponse::setContent
     * @covers HttpResponse::getContent
     */
    public function testSetContent()
    {
        $content = 'Some Content. This is the response body.';
        HttpResponse::setContent($content);
        $this->assertEquals($content, HttpResponse::getContent());

        // append content
        $content2 = 'Some additional content to test appending.';
        HttpResponse::setContent($content2);
        $this->assertEquals($content . $content2, HttpResponse::getContent());

        // replace content
        $content3 = ' This new Content replaces the old content.'
        HttpResponse::setContent($content3, true);
        $this->assertEquals($content3, HttpResponse::getContent());
    }

    /**
     * @covers HttpResponse::setContentType
     * @covers HttpResponse::getContentType
     */
    public function testSetAndGetContentType()
    {
        // default type
        $this->assertEquals('html', HttpResponse::getContentType());

        $type = 'xml';
        HttpResponse::setContentType('xml');
        $this->assertEquals($type, HttpResponse::getContentType());
    }

    /**
     * @covers HttpResponse::setContentType
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Specified type not valid. Use: html, txt, xml or json.
     */
    public function testSetContentType_throws()
    {
        HttpResponse::setContentType('SomeInvalidType');
    }


}
