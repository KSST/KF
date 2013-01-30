<?php
namespace KochTest\Form\Decorators\Form;

use Koch\Form\Decorators\Form\Div;

class DivTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Div
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Div;
    }

    protected function tearDown()
    {
    }

    /**
     * @covers Koch\Form\Decorators\Form\Div::render
     */
    public function testRender()
    {
        $this->object->setClass('testCssClass');
        $this->object->setId('testID');

        $result = $this->object->render('formcontent');

        $this->assertEquals(CR.'<div class="testCssClass" id="testID">formcontent</div>'.CR, $result);
    }
}
