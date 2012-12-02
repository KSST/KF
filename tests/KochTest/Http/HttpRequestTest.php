<?php

namespace Koch\Http;

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

    public function testMethod_contructor_unsetsGlobalVars()
    {
        $this->assertFalse(isset($_REQUEST));

        //  Undefined variable: GLOBALS
        //  it's not possible to unset globals - phpunit side effect?
        //$this->assertFalse(isset($GLOBALS));
    }

    public function testMethod_getRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'someMethod';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'OverrideMethodName';
        $this->assertEquals('OverrideMethodName', HttpRequest::getRequestMethod());

        unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $this->assertEquals('GET', HttpRequest::getRequestMethod());

        $this->request->setRequestMethod('BEAVIS');
        $this->assertEquals('BEAVIS', HttpRequest::getRequestMethod());
    }

    public function testMethod_setRequestMethod()
    {
        $this->request->setRequestMethod('BUTTHEAD');
        $this->assertEquals('BUTTHEAD', HttpRequest::getRequestMethod());
    }

    public function testMethod_isGET()
    {
        $this->request->setRequestMethod('GET');
        $this->assertTrue($this->request->isGet());
    }

    public function testMethod_isPOST()
    {
        $this->request->setRequestMethod('POST');
        $this->assertTrue($this->request->isPost());
    }

    public function testMethod_isPUT()
    {
        $this->request->setRequestMethod('PUT');
        $this->assertTrue($this->request->isPut());
    }

    public function testMethod_isDELETE()
    {
        $this->request->setRequestMethod('DELETE');
        $this->assertTrue($this->request->isDelete());
    }

    public function testMethod_isAjax()
    {
        $isAjax = $this->request->isAjax();
		$this->assertFalse($isAjax);

        $_SERVER['X-Requested-With'] = 'XMLHttpRequest';
		$isAjax = $this->request->isAjax();
		$this->assertTrue($isAjax);

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$isAjax = $this->request->isAjax();
		$this->assertTrue($isAjax);
    }

    /*public function testMethod_getPost()
    {
        $_POST['POST-ABC'] = '123';
        $result = $this->request->getPost();
        $this->assertArrayHasKey('POST-ABC', $result);

        // ArrayAccess via offsetExists
        $this->assertEquals($this->request['POST-ABC'], '123');
    }

    public function testMethod_getGet()
    {
        $_GET['GET-ABC'] = '123';
        $result = $this->request->getGET();
        $this->assertArrayHasKey('GETABC', $result);
    }*/

    public function testMethod_getServerProtocol()
    {
		$this->assertEquals($this->request->getServerProtocol(), 'http://');

		$_SERVER['HTTPS'] = 'on';
		$this->assertEquals($this->request->getServerProtocol(), 'https://');
	}

    public function testMethod_IsSecure()
    {
        $_SERVER['HTTPS'] = 'NO';
		$this->assertFalse($this->request->isSecure());

        $_SERVER['HTTPS'] = '1';
		$this->assertTrue($this->request->isSecure());
	}

    public function testMethod_getBaseURL()
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

    public function testMethod_getServerName()
    {
        $name = 'ServerName';
        $_SERVER['SERVER_NAME'] = $name;
        $this->assertEquals($this->request->getServerName(), $name);
    }
}
