<?php

namespace KochTest\Http;

use Koch\Http\HttpRequest;

class HttpRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->request = new HttpRequest;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->request);
    }

    public function testMethodcontructor_unsetsGlobalVars()
    {
        $this->assertFalse(isset($_REQUEST));

        //  Undefined variable: GLOBALS
        //  it's not possible to unset globals - phpunit side effect?
        //$this->assertFalse(isset($GLOBALS));
    }

    public function testMethodgetRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'someMethod';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'OverrideMethodName';
        $this->assertEquals('OverrideMethodName', HttpRequest::getRequestMethod());

        unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $this->assertEquals('GET', HttpRequest::getRequestMethod());

        $this->request->setRequestMethod('BEAVIS');
        $this->assertEquals('BEAVIS', HttpRequest::getRequestMethod());

        unset($_SERVER['REQUEST_METHOD']);
    }

    public function testMethodsetRequestMethod()
    {
        $this->request->setRequestMethod('BUTTHEAD');
        $this->assertEquals('BUTTHEAD', HttpRequest::getRequestMethod());
    }

    public function testMethodisGET()
    {
        $this->request->setRequestMethod('GET');
        $this->assertTrue($this->request->isGet());
    }

    public function testMethodisPOST()
    {
        $this->request->setRequestMethod('POST');
        $this->assertTrue($this->request->isPost());
    }

    public function testMethodisPUT()
    {
        $this->request->setRequestMethod('PUT');
        $this->assertTrue($this->request->isPut());
    }

    public function testMethodisDELETE()
    {
        $this->request->setRequestMethod('DELETE');
        $this->assertTrue($this->request->isDelete());
    }

    public function testMethodisAjax()
    {
        $isAjax = $this->request->isAjax();
        $this->assertFalse($isAjax);

        $_SERVER['X-Requested-With'] = 'XMLHttpRequest';
        $isAjax = $this->request->isAjax();
        $this->assertTrue($isAjax);

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $isAjax = $this->request->isAjax();
        $this->assertTrue($isAjax);

        unset($_SERVER['X-Requested-With']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    /*public function testMethodgetPost()
    {
        $_POST['POST-ABC'] = '123';
        $result = $this->request->getPost();
        $this->assertArrayHasKey('POST-ABC', $result);

        // ArrayAccess via offsetExists
        $this->assertEquals($this->request['POST-ABC'], '123');
    }

    public function testMethodgetGet()
    {
        $_GET['GET-ABC'] = '123';
        $result = $this->request->getGET();
        $this->assertArrayHasKey('GETABC', $result);
    }*/

    public function testMethodgetServerProtocol()
    {
        $this->assertEquals($this->request->getServerProtocol(), 'http://');

        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals($this->request->getServerProtocol(), 'https://');
    }

    public function testMethodIsSecure()
    {
        $_SERVER['HTTPS'] = 'NO';
        $this->assertFalse($this->request->isSecure());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue($this->request->isSecure());
    }

    public function testMethodgetBaseURL()
    {
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $this->assertEquals($this->request->getBaseURL(), 'http://localhost');

        /*$_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 123;
        $this->assertEquals($this->request->getBaseURL(), 'https://localhost:123');

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals($this->request->getBaseURL(), 'https://localhost');*/
    }

    public function testMethodgetServerName()
    {
        $name = 'ServerName';
        $_SERVER['SERVER_NAME'] = $name;
        $this->assertEquals($this->request->getServerName(), $name);
    }
}
