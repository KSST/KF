<?php

namespace KochTest\View\Renderer;

use Koch\View\Renderer\Xtemplate;

class XtemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Xtemplate
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (false === class_exists('XTemplate')) {
            $this->markTestSkipped('This test requires the vendor library "XTemplate".');
        }

        $options = [];

        $this->object = new Xtemplate($options);
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
     * @covers Koch\View\Renderer\Xtemplate::initializeEngine
     */
    public function testInitializeEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        /* $this->object->initializeEngine();

          $this->assertInstanceOf('Xtemplate', $this->renderer); */
    }

    /**
     * @covers Koch\View\Renderer\Xtemplate::configureEngine
     */
    public function testConfigureEngine()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Xtemplate::renderPartial
     *
     * @todo   Implement testRenderPartial().
     */
    public function testRenderPartial()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Xtemplate::clearVars
     *
     * @todo   Implement testClearVars().
     */
    public function testClearVars()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Xtemplate::clearCache
     *
     * @todo   Implement testClearCache().
     */
    public function testClearCache()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\View\Renderer\Xtemplate::fetch
     *
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
     * @covers Koch\View\Renderer\Xtemplate::display
     *
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
     * @covers Koch\View\Renderer\Xtemplate::render
     *
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
     * @covers Koch\View\Renderer\Xtemplate::assign
     *
     * @todo   Implement testAssign().
     */
    public function testAssign()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
