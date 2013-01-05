<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Smarty;

class SmartyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Smarty
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $options = array();

        $this->object = new Smarty($options);
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
     * @covers Koch\View\Renderer\Smarty::initializeEngine
     */
    public function testInitializeEngine()
    {
        $this->object->initializeEngine();

        $this->assertTrue(is_object($this->object->renderer));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::configureEngine
     * @todo   Implement testConfigureEngine().
     */
    public function testConfigureEngine()
    {
        $this->object->configureEngine();

        // lets accept as configured, if template paths are set
        $this->assertTrue(is_array($this->object->getTemplatePaths()));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setTemplatePath
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Smarty Template path provided: Path not existing or not readable.
     */
    public function testSetTemplatePath_throwsException()
    {
       $this->object->setTemplatePath('/path/to/where/the/smarty/flavour/is/not');
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setTemplatePath
     * @covers Koch\View\Renderer\Smarty::getTemplatePaths
     */
    public function testSetTemplatePath()
    {
       $this->object->setTemplatePath(__DIR__);
       $paths = $this->object->getTemplatePaths();

       $this->assertEquals($paths[0], __DIR__ . DIRECTORY_SEPARATOR);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::assign
     */
    public function testAssign()
    {
        /* key - value */
        $this->object->assign('key1', 'value');
        $this->assertEquals($this->object->key1, 'value');

        /* array */
        $array1 = array(
            'A' => '1',
            'B' => '2'
        );
        $this->object->assign($array1);

        $this->assertEquals($this->object->A, 1);
        $this->assertEquals($this->object->B, 2);

        /* multi-dim array */
        $array2 = array(
            'C' => array(
                'D' => '4'
        ));
        $this->object->assign($array2);

        $this->assertTrue(is_array($this->object->C));
        $this->assertEquals($this->object->C['D'], 4);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__get
     */
    public function test__get()
    {
        $value = 'value1';
        $this->object->templatevar1 = $value;

        $this->assertEquals($this->object->templatevar1, $value);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__set
     */
    public function test__set()
    {
        $value = 'value1';
        $this->object->templatevar1 = $value;

        $vars = $this->object->getVars();
        $this->assertArrayHasKey('templatevar1', $vars);
        $this->assertEquals($vars['templatevar1'], $value);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__isset
     */
    public function test__isset()
    {
        $this->object->templatevar1 = 'value1';

        $this->assertTrue(isset($this->object->templatevar1));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__unset
     */
    public function test__unset()
    {
        $this->object->templatevar1 = 'value1';
        unset($this->object->templatevar1);

        $this->assertFalse(isset($this->object->templatevar1));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::fetch
     * @todo   Implement testFetch().
     */
    public function testFetch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Smarty::display
     * @todo   Implement testDisplay().
     */
    public function testDisplay()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Smarty::getVars
     */
    public function testGetVars()
    {
        $value = 'value1';
        $this->object->templatevar1 = $value;

        $vars = $this->object->getVars();

        $this->assertTrue(is_array($vars));
        $this->assertArrayHasKey('templatevar1', $vars);
        $this->assertEquals($vars['templatevar1'], $value);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::clearVars
     */
    public function testClearVars()
    {
        $value = 'value1';
        $this->object->templatevar1 = $value;

        $vars = $this->object->getVars();

        $this->assertTrue(is_array($vars));
        $this->assertArrayHasKey('templatevar1', $vars);
        $this->assertEquals($vars['templatevar1'], $value);

        $this->object->clearVars();

        $vars = $this->object->getVars();
        $this->assertEquals(1, count($vars));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::resetCache
     * @todo   Implement testResetCache().
     */
    public function testResetCache()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Smarty::activateCaching
     * @todo   Implement testActivateCaching().
     */
    public function testActivateCaching()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setRenderMode
     * @covers Koch\View\Renderer\Smarty::getRenderMode
     */
    public function testSetRenderMode()
    {
       // default mode
       $this->assertEquals('LAYOUT', $this->object->getRenderMode());

       $mode = 'NOLAYOUT';
       $this->object->setRenderMode($mode);
       $this->assertEquals($mode, $this->object->getRenderMode());
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setRenderMode
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Use LAYOUT or NOLAYOUT as parameter.
     */
    public function testSetRenderMode_throwsException()
    {
       $mode = 'BLA';
       $this->object->setRenderMode($mode);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::render
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
     * @covers Koch\View\Renderer\Smarty::renderPartial
     * @todo   Implement testRenderPartial().
     */
    public function testRenderPartial()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
