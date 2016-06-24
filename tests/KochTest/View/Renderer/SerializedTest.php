<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Serialized;

class SerializedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serialized
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $options = [];

        $this->object = new Serialized($options);
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
     * @covers Koch\View\Renderer\Serialized::render
     */
    public function testRender()
    {
        $this->object->assign('key', 'value');

        $r = $this->object->render();

        $expectedOutput = 'a:1:{s:3:"key";s:5:"value";}';
        $this->assertEquals($expectedOutput, $r);
    }

    /**
     * @covers Koch\View\Renderer\Serialized::assign
     */
    public function testAssign()
    {
        // key => value
        $this->object->assign('key', 'value');
        $this->assertEquals('value', $this->object->viewdata['key']);

        // array
        $array = ['key' => 'value'];
        $this->object->assign($array);
        $this->assertEquals('value', $this->object->viewdata['key']);

        // object
        $object      = new \stdClass();
        $object->key = 'value';
        $this->object->assign($object);
        $this->assertEquals('value', $this->object->viewdata['key']);
    }

    /**
     * @covers Koch\View\Renderer\Serialized::configureEngine
     */
    public function testConfigureEngine()
    {
        $this->assertNull($this->object->configureEngine());
    }

    /**
     * @covers Koch\View\Renderer\Serialized::display
     */
    public function testDisplay()
    {
        $this->object->assign('key', 'value');
        $this->object->display();

        $expectedString = 'a:1:{s:3:"key";s:5:"value";}';

        $this->expectOutputString($expectedString);
    }

    /**
     * @covers Koch\View\Renderer\Serialized::fetch
     */
    public function testFetch()
    {
        $this->object->assign('key', 'value');
        $r = $this->object->fetch();

        $expectedString = 'a:1:{s:3:"key";s:5:"value";}';

        $this->assertEquals($r, $expectedString);
    }

    /**
     * @covers Koch\View\Renderer\Serialized::initializeEngine
     */
    public function testinitializeEngine()
    {
        $this->assertNull($this->object->initializeEngine());
    }
}
