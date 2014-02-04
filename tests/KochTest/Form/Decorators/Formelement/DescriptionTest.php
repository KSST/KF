<?php
namespace KochTest\Form\Decorators\Formelement;

use Koch\Form\Decorators\Formelement\Description;

class DescriptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var object \Koch\Form\Decorators\Formelement\Description
     */
    protected $object;

    protected $formelement;

    protected function setUp()
    {
        $this->formelement = new \Koch\Form\Elements\Checkbox();
        $this->formelement->setDescription('My Description');

        $this->object = new Description;
        $this->object->decorateWith($this->formelement);
    }

    protected function tearDown()
    {
    }

    /**
     * @covers Koch\Form\Decorators\Formelement\Description::render
     */
    public function testRender()
    {
        $result = $this->object->render('');

        $expected = '<br />' . CR . '<span class="formdescription">My Description</span>' . CR;

        $this->assertEquals($expected, $result);
    }

    public function testCanSetDescription()
    {
        $this->object->setDescription('Another Description');
        $result = $this->object->render('');

        $expected = '<br />' . CR . '<span class="formdescription">Another Description</span>' . CR;

        $this->assertEquals($expected, $result);
    }

    public function testCanSetCssClass()
    {
        $cssClass = 'css-class';
        $this->object->setCssClass($cssClass);
        $result = $this->object->render('');

        $this->assertContains($cssClass, $result);
        $this->assertContains($cssClass, $this->object->getClass());
    }
}
