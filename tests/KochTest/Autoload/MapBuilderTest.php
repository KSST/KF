<?php
namespace KochTest\Autoload;

use Koch\Autoload\MapBuilder;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class MapBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MapBuilder
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new MapBuilder;

        vfsStreamWrapper::register();

        $this->vfsFileURL = vfsStream::url('root/classmap.file');
        $this->file = vfsStream::newFile('classmap.file', 0777);

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        vfsStreamWrapper::setRoot($this->root);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Autoload\MapBuilder::build
     */
    public function testBuild()
    {
       // create classmap
       $this->object->build(
           array(__DIR__ . '/../../../framework/Koch/Config/Adapter'),
           $this->vfsFileURL
       );

       // include classmap array
       $classmap = include $this->vfsFileURL;

       $this->assertTrue(is_array($classmap));
       $this->assertArrayHasKey('Koch\Config\Adapter\CSV', $classmap);
    }

    /**
     * @covers Koch\Autoload\MapBuilder::extractClassname
     * @todo   Implement testExtractClassname().
     */
    public function testExtractClassname()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Autoload\MapBuilder::writeMapFile
     * @todo   Implement testWriteMapFile().
     */
    public function testWriteMapFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}