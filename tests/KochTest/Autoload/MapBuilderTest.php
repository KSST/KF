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

        $this->vfsFileWithPHPClass = vfsStream::url('root/class.php');
        $content = '<?php class MyClass(){} ?>';
        $this->file2 = vfsStream::newFile('class.php', 0777)->setContent($content);

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        $this->root->addChild($this->file2);

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
     * @covers Koch\Autoload\MapBuilder::extractClassnames
     */
    public function testExtractClassnames()
    {
        $classname = $this->object->extractClassnames($this->vfsFileWithPHPClass);

        $this->assertEquals($classname[0], 'MyClass');
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
