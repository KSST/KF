<?php

namespace KochTest\Http;

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
        //$this->response = new HttpResponse;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        //unset($this->response);
    }

    public function testProperty_DefaultStatusIs200()
    {
        $this->assertEquals(200, HttpResponse::getStatusCode());
    }

    /**
     * @covers Koch\Http\HttpResponse::setStatusCode
     * @covers Koch\Http\HttpResponse::getStatusCode
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
     * @covers Koch\Http\HttpResponse::setContent
     * @covers Koch\Http\HttpResponse::getContent
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
        $content3 = ' This new Content replaces the old content.';
        HttpResponse::setContent($content3, true);
        $this->assertEquals($content3, HttpResponse::getContent());
    }

    /**
     * @covers Koch\Http\HttpResponse::setContentType
     * @covers Koch\Http\HttpResponse::getContentType
     */
    public function testSetAndGetContentType()
    {
        // default type
        $this->assertEquals('text/html', HttpResponse::getContentType());

        HttpResponse::setContentType('xml');
        $this->assertEquals('application/xml', HttpResponse::getContentType());
    }

    /**
     * @covers Koch\Http\HttpResponse::setContentType
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Specified type not valid. Use: html, txt, xml or json.
     */
    public function testSetContentType_throws()
    {
        HttpResponse::setContentType('SomeInvalidType');
    }

    /**
     * @covers Koch\Http\HttpResponse::addHeader
     */
    public function testAddHeader()
    {
        $name = 'TestName';
        $value = 'TestValue';
        HttpResponse::addHeader($name, $value);

        $this->assertArrayHasKey($name,  self::reflectProperty('headers')->getValue());
    }

    /**
     * ReflectProperty reflects a class property,
     * changing its scope to public.
     * This is used to access and test private properties
     * for which no getters are implemented in the public api.
     *
     * @param  string           $name Property name.
     * @return \ReflectionClass
     */
    protected static function reflectProperty($name)
    {
        $class = new \ReflectionClass('Koch\Http\HttpResponse');
        $method = $class->getProperty($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * @covers Koch\Http\HttpResponse::addHeader
     * @covers Koch\Http\HttpResponse::setContent
     * @covers Koch\Http\HttpResponse::clearHeaders
     * @covers Koch\Http\HttpResponse::getContent
     */
    public function testClearHeaders()
    {
        HttpResponse::addHeader('SomeHeader', 'SomeValue');
        HttpResponse::setContent('Some Content.');

        $this->assertTrue(HttpResponse::clearHeaders());

        $this->assertArrayNotHasKey('SomeHeader',  self::reflectProperty('headers')->getValue());
        $this->assertNull(HttpResponse::getContent());
    }

    public function testSetNoCacheHeader()
    {
         HttpResponse::setNoCacheHeader();

         $this->assertArrayHasKey('Pragma', self::reflectProperty('headers')->getValue());
         $this->assertArrayHasKey('Cache-Control', self::reflectProperty('headers')->getValue());
         $this->assertArrayHasKey('Expires',  self::reflectProperty('headers')->getValue());
         $this->assertArrayHasKey('Last-Modified', self::reflectProperty('headers')->getValue());
    }

    public function testSendResponse()
    {
        $content = 'Some Body';
        HttpResponse::setContent($content);
        HttpResponse::addHeader('Header', 'Content');
        HttpResponse::addHeader('Header2', 'Content2');

        ob_start();
        HttpResponse::sendResponse();
        $result = ob_get_clean();

        $this->assertEquals($content, $result);
    }
}
