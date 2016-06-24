<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Json
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $options = [];

        $this->object = new Json($options);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\View\Renderer\Json::initializeEngine
     */
    public function testInitializeEngine()
    {
        $this->assertNull($this->object->initializeEngine());
    }

    /**
     * @covers Koch\View\Renderer\Json::configureEngine
     */
    public function testConfigureEngine()
    {
        $this->assertNull($this->object->configureEngine());
    }

    /**
     * @covers Koch\View\Renderer\Json::jsonEncode
     */
    public function testJsonEncode()
    {
        // default value
       $this->assertEquals('[]', $this->object->jsonEncode());

       // with data
       $data    = ['key' => 'value'];
        $result = $this->object->jsonEncode($data);
        $this->assertEquals('{"key":"value"}', $result);
    }

    /**
     * @covers Koch\View\Renderer\Json::renderAsHeader
     */
    public function testRenderAsHeader()
    {
        $data = ['key' => 'value'];
        $this->assertTrue($this->object->renderAsHeader($data));
    }

    /**
     * @covers Koch\View\Renderer\Json::render
     */
    public function testRender()
    {
        $r = $this->object->render('', ['key' => 'value']);
        $this->assertEquals('{"key":"value"}', $r);
    }

    /**
     * @covers Koch\View\Renderer\Json::assign
     */
    public function testAssign()
    {
        $this->object->assign('key', 'value');
        $this->assertEquals('value', $this->object->viewdata['key']);
    }

    /**
     * @covers Koch\View\Renderer\Json::display
     */
    public function testDisplay()
    {
        $this->object->display('', ['key' => 'value']);

        $this->expectOutputString('{"key":"value"}');
    }

    /**
     * @covers Koch\View\Renderer\Json::fetch
     */
    public function testFetch()
    {
        $r = $this->object->fetch('', ['key' => 'value']);
        $this->assertEquals('{"key":"value"}', $r);
    }
}
