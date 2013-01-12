<?php

namespace KochTest\View\Renderer;

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
     */
    protected function setUp()
    {
        // With the PHP renderer one might use short_open_tags in templates.
        // They are enabled by default from PHP 5.4 on, but were disabled by default in PHP 5.3.
        if(version_compare(PHP_VERSION, '5.4.0', '<') and (ini_get('short_open_tag') == false)) {
            $this->markTestSkipped('This test requires the php.ini option "short_open_tag=on".');
        }

        $options = array();

        $this->object = new Php($options);

        vfsStreamWrapper::register();
        $this->templateFileURL = vfsStream::url('root/php-renderer.tpl');
        $this->file = vfsStream::newFile('php-renderer.tpl', 0777)->withContent($this->getTemplateContent());
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
        // alternative placeholder syntax (php shorttag)
        // normal syntax (normal php tags)
        return 'Hello <?=$placeholder?>. The <?php echo $placeholder; ?> is not enough.';
    }

    /**
     * @covers Koch\View\Renderer\Php::fetch
     */
    public function testFetch()
    {
        $template = $this->templateFileURL;
        $data = array('placeholder' => 'World');

        $result = $this->object->fetch($template, $data);

        $this->assertEquals('Hello World. The World is not enough.', $result);
    }

    /**
     * @covers Koch\View\Renderer\Php::assign
     */
    public function testAssign()
    {
        // key => value
        $this->object->assign('key', 'value');
        $this->assertEquals('value', $this->object->viewdata['key']);

        // array
        $array = array('key' => 'value');
        $this->object->assign($array);
        $this->assertEquals('value', $this->object->viewdata['key']);

        // object
        $object = new \stdClass;
        $object->key = 'value';
        $this->object->assign($object);
        $this->assertEquals('value', $this->object->viewdata['key']);
    }

    /**
     * @covers Koch\View\Renderer\Php::render
     */
    public function testRender()
    {
       $template = $this->templateFileURL;
       $viewdata = array('placeholder' => 'World');

       $result = $this->object->render($template, $viewdata);

       $this->assertEquals('Hello World. The World is not enough.', $result);
    }

    /**
     * @covers Koch\View\Renderer\Php::configureEngine
     */
    public function testConfigureEngine()
    {
        $this->assertNull($this->object->configureEngine());
    }

    /**
     * @covers Koch\View\Renderer\Php::display
     */
    public function testDisplay()
    {
       $template = $this->templateFileURL;
       $viewdata = array('placeholder' => 'World');

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
}
