<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Xslt;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class XsltTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Xslt
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!extension_loaded('libxml') or extension_loaded('xsl') === false) {
            $this->markTestSkipped('This test requires the PHP extension "xsl" or "libxml".');
        }

        $options = array();

        $this->object = new Xslt($options);

        vfsStreamWrapper::register();

        $this->stylesheetFileURL = vfsStream::url('root/stylesheet.xsl');
        $this->file = vfsStream::newFile('stylesheet.xsl', 0777)->withContent($this->getStylesheetContent());

        $this->dataFileURL = vfsStream::url('root/data.xml');
        $this->file2 = vfsStream::newFile('data.xml', 0777)->withContent($this->getDataContent());

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        $this->root->addChild($this->file2);
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
      * @return string content of "stylesheet.xsl"
      */
    public function getStylesheetContent()
    {
        return <<< EOF
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
<greeting>
Hello, <xsl:value-of select="/root/@name"/>
</greeting>
</xsl:template>

</xsl:stylesheet>
EOF;
    }

    /**
     * @return string content of "data.xml"
     */
    public function getDataContent()
    {
        return '<root name="World"/>';
    }

    /**
     * @covers Koch\View\Renderer\Xslt::setStylesheet
     * @covers Koch\View\Renderer\Xslt::getStylesheet
     */
    public function testSetStylesheet()
    {
       $s = 'abc';
       $this->object->setStylesheet($s);
       $this->assertEquals($s, $this->object->getStylesheet());
    }

    /**
     * @covers Koch\View\Renderer\Xslt::render
     * @todo   Implement testRender().
     */
    public function testRender()
    {
        $this->object->setStylesheet($this->stylesheetFileURL);

        $this->object->render($this->dataFileURL);

        $expectedOutput = <<< EOF
<?xml version="1.0"?>
<greeting>
Hello, World</greeting>

EOF;
        $this->expectOutputString($expectedOutput);
    }

    /**
     * @covers Koch\View\Renderer\Xslt::assign
     * @todo   Implement testAssign().
     */
    public function testAssign()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Xslt::configureEngine
     * @todo   Implement testConfigureEngine().
     */
    public function testConfigureEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Xslt::display
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
     * @covers Koch\View\Renderer\Xslt::fetch
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
     * @covers Koch\View\Renderer\Xslt::initializeEngine
     * @todo   Implement testInitializeEngine().
     */
    public function testInitializeEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
