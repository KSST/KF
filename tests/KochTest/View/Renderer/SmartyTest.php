<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Smarty;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

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
        $options = [];

        $this->object = new Smarty($options);

        $this->object->renderer->debugging = false;

        vfsStreamWrapper::register();
        $this->templateFileURL = vfsStream::url('root/smarty-renderer.tpl');
        $this->file            = vfsStream::newFile('smarty-renderer.tpl', 0777)
            ->withContent($this->getTemplateContent());
        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    public function getTemplateContent()
    {
        // smarty default template syntax
        return 'Hello {$placeholder}.';
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
     *
     * @todo   Implement testConfigureEngine().
     */
    public function testConfigureEngine()
    {
        $this->object->configureEngine();

        // lets accept as configured, if template paths are set
        $this->assertTrue(is_array($this->object->getTemplatePaths()));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::getEngine
     */
    public function testGetEngine()
    {
        $this->object->getEngine();

        $this->assertTrue(is_object($this->object->renderer));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setTemplatePath
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Smarty Template path provided: Path not existing or not readable.
     */
    public function testSetTemplatePathThrowsException()
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
        $array1 = [
            'A' => '1',
            'B' => '2',
        ];
        $this->object->assign($array1);

        $this->assertEquals($this->object->A, 1);
        $this->assertEquals($this->object->B, 2);

        /* multi-dim array */
        $array2 = [
            'C' => [
                'D' => '4',
        ], ];
        $this->object->assign($array2);

        $this->assertTrue(is_array($this->object->C));
        $this->assertEquals($this->object->C['D'], 4);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__get
     * @covers Koch\View\AbstractRenderer::__get
     */
    public function testMagicGet()
    {
        $value                      = 'value1';
        $this->object->templatevar1 = $value;

        $this->assertEquals($this->object->templatevar1, $value);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__set
     * @covers Koch\View\AbstractRenderer::__set
     */
    public function testMagicSet()
    {
        $value                      = 'value1';
        $this->object->templatevar1 = $value;

        $vars = $this->object->getVars();
        $this->assertArrayHasKey('templatevar1', $vars);
        $this->assertEquals($vars['templatevar1'], $value);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__isset
     * @covers Koch\View\AbstractRenderer::__isset
     */
    public function testMagicIsset()
    {
        $this->object->templatevar1 = 'value1';

        $this->assertTrue(isset($this->object->templatevar1));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::__unset
     * @covers Koch\View\AbstractRenderer::__unset
     */
    public function testMagicUnset()
    {
        $this->object->templatevar1 = 'value1';
        unset($this->object->templatevar1);

        $this->assertFalse(isset($this->object->templatevar1));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::fetch
     */
    public function testFetch()
    {
        $this->object->assign('placeholder', 'World');

        $result = $this->object->fetch($this->templateFileURL);

        $expectedTemplateContent = <<< EOF

<!-- [-Start-] Included Template vfs://root/smarty-renderer.tpl -->
Hello World.
<!-- [-End-] Included Template vfs://root/smarty-renderer.tpl  -->

EOF;
        $this->assertEquals($expectedTemplateContent, $result);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::createCacheId
     */
    public function testCreateCacheId()
    {
        $cache_id = $this->object->createCacheId();

        $this->assertTrue(is_string($cache_id));
        $this->assertTrue(strlen($cache_id) === 32);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::display
     */
    public function testDisplay()
    {
        $this->object->assign('placeholder', 'World');

        $result = $this->object->display($this->templateFileURL);

        $expectedTemplateContent = <<< EOF

<!-- [-Start-] Included Template vfs://root/smarty-renderer.tpl -->
Hello World.
<!-- [-End-] Included Template vfs://root/smarty-renderer.tpl  -->

EOF;
        $this->expectOutputString($expectedTemplateContent);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::getVars
     */
    public function testGetVars()
    {
        $value                      = 'value1';
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
        $value                      = 'value1';
        $this->object->templatevar1 = $value;

        $vars = $this->object->getVars();

        $this->assertTrue(is_array($vars));
        $this->assertArrayHasKey('templatevar1', $vars);
        $this->assertEquals($vars['templatevar1'], $value);

        $this->object->clearVars();

        $varsB = $this->object->getVars();
        $this->assertEquals(1, count($varsB));
    }

    /**
     * @covers Koch\View\Renderer\Smarty::resetCache
     */
    public function testResetCache()
    {
        $this->assertTrue($this->object->resetCache());
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setRenderMode
     * @covers Koch\View\Renderer\Smarty::getRenderMode
     */
    public function testSetRenderMode()
    {
        // default mode
        $this->assertEquals('LAYOUT', $this->object->getRenderMode());

        $mode = 'PARTIAL';
        $this->object->setRenderMode($mode);
        $this->assertEquals($mode, $this->object->getRenderMode());
    }

    /**
     * @covers Koch\View\Renderer\Smarty::setRenderMode
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Use LAYOUT or PARTIAL as parameter.
     */
    public function testSetRenderModeThrowsException()
    {
        $mode = 'BLA';
        $this->object->setRenderMode($mode);
    }

    /**
     * @covers Koch\View\Renderer\Smarty::render
     *
     * @todo   Implement testRender().
     */
    public function testRender()
    {
        $this->object->assign('placeholder', 'World');

        $this->object->setRenderMode('PARTIAL');

        $result = $this->object->render($this->templateFileURL);

        $expectedTemplateContent = <<< EOF

<!-- [-Start-] Included Template vfs://root/smarty-renderer.tpl -->
Hello World.
<!-- [-End-] Included Template vfs://root/smarty-renderer.tpl  -->

EOF;
        $this->assertEquals($expectedTemplateContent, $result);
    }
}
