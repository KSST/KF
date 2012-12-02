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
}
