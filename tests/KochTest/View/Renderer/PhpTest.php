<?php

namespace KochTest\View\Renderer;

use Koch\View\Mapper;
use Koch\View\Renderer\Php;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Php
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers Koch\View\AbstractRenderer::__construct
     */
    protected function setUp()
    {
        // With the PHP renderer one might use short_open_tags in templates.
        // They are enabled by default from PHP 5.4 on, but were disabled by default in PHP 5.3.
        if (version_compare(PHP_VERSION, '5.4.0', '<') and (ini_get('short_open_tag') === false)) {
            $this->markTestSkipped('This test requires the php.ini option "short_open_tag=on".');
        }

        $options = [];

        $this->object = new Php($options);

        vfsStreamWrapper::register();
        $this->templateFileURL = vfsStream::url('root/php-renderer.tpl');
        $this->file            = vfsStream::newFile('php-renderer.tpl', 0777)->withContent($this->getTemplateContent());
        $this->root            = new vfsStreamDirectory('root');
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

    /**
     * @covers Koch\View\Renderer\Php::__construct
     * @covers Koch\View\AbstractRenderer::getOptions
     * @covers Koch\View\AbstractRenderer::setOptions
     */
    public function testPassingOptionsViaConstructor()
    {
        $options      = ['optionA' => 'value'];
        $this->object = new Php($options);

        $this->assertTrue(isset($this->object->options['optionA']));
        $this->assertArrayHasKey('optionA', $this->object->getOptions());
    }

    public function getTemplateContent()
    {
        // alternative placeholder syntax (php shorttag)
        // normal syntax (normal php tags)
        return 'Hello <?=$placeholder?>. The <?php echo $placeholder; ?> is not enough.';
    }

    /**
     * @covers Koch\View\Renderer\Php::fetch
     * @covers Koch\View\AbstractRenderer::fetch
     */
    public function testFetch()
    {
        $template = $this->templateFileURL;
        $data     = ['placeholder' => 'World'];

        $result = $this->object->fetch($template, $data);

        $this->assertEquals('Hello World. The World is not enough.', $result);
    }

    /**
     * @covers Koch\View\Renderer\Php::assign
     * @covers Koch\View\AbstractRenderer::assign
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
     * @covers Koch\View\Renderer\Php::render
     * @covers Koch\View\AbstractRenderer::render
     */
    public function testRender()
    {
        $template = $this->templateFileURL;
        $viewdata = ['placeholder' => 'World'];

        $result = $this->object->render($template, $viewdata);

        $this->assertEquals('Hello World. The World is not enough.', $result);
    }

    /**
     * @covers Koch\View\Renderer\Php::configureEngine
     * @covers Koch\View\AbstractRenderer::configureEngine
     */
    public function testConfigureEngine()
    {
        $this->assertNull($this->object->configureEngine());
    }

    /**
     * @covers Koch\View\Renderer\Php::display
     * * @covers Koch\View\AbstractRenderer::display
     */
    public function testDisplay()
    {
        $template = $this->templateFileURL;
        $viewdata = ['placeholder' => 'World'];

        $this->object->display($template, $viewdata);

        $this->expectOutputString('Hello World. The World is not enough.');
    }

    /**
     * @covers Koch\View\Renderer\Php::initializeEngine
     */
    public function testInitializeEngine()
    {
        $this->assertNull($this->object->initializeEngine());
    }

    /**
     * Test methods of the AbstractRenderer.
     */

    /**
     * @covers Koch\View\AbstractRenderer::getEngine
     */
    public function testGetEngine()
    {
        $this->assertNull($this->object->getEngine());
    }

    /**
     * @covers Koch\View\AbstractRenderer::clearVars
     */
    public function testClearVars()
    {
        $this->assertNull($this->object->clearVars());
    }

    /**
     * @covers Koch\View\AbstractRenderer::getVars
     */
    public function testGetVars()
    {
        // key => value
        $this->object->assign('key', 'value');
        $this->assertEquals('value', $this->object->viewdata['key']);

        $this->assertArrayHasKey('key', $this->object->getVars());
    }

    /**
     * @covers Koch\View\AbstractRenderer::setViewMapper
     * @covers Koch\View\AbstractRenderer::getViewMapper
     */
    public function testGetSetViewMapper()
    {
        $viewMapper = new Mapper();
        $this->object->setViewMapper($viewMapper);
        $this->assertEquals($viewMapper, $this->object->getViewMapper());
    }

    /**
     * @covers Koch\View\AbstractRenderer::getTemplate
     * @covers Koch\View\AbstractRenderer::setTemplate
     */
    public function testGetSetTemplate()
    {
        $template = 'bubble-burst.dot.com';
        $this->object->setTemplate($template);
        $this->assertEquals($template, $this->object->getTemplate());
    }

    /**
     * @covers Koch\View\AbstractRenderer::autoEscape
     */
    public function testAutoEscape()
    {
        $key1   = 'key1';
        $value1 = 'value1';

        $this->object->autoEscape($key1, $value1);
        $this->assertEquals(['key1' => 'value1'], $this->object->getVars());

        $this->object->clearVars();

        // array
        $key2   = 'key2';
        $value2 = ['value2a', 'value2b'];
        $this->object->autoEscape($key2, $value2);

        $expected = ['key2' => [0 => 'value2a', 1 => 'value2b']];
        $this->assertEquals($expected, $this->object->getVars());
    }
}
