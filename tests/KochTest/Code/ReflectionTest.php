<?php
namespace Koch\Code;

class ReflectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reflection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Reflection('Koch\Code\Reflection');
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
     * @covers Koch\Code\Reflection::setClassName
     * @covers Koch\Code\Reflection::__construct
     */
    public function testSetClassName()
    {
        // set class via constructor
        $this->object = new Reflection('A');
        $this->assertEquals('A', $this->object->getClassName());

        // set class via method
        $this->object->setClassName('B');
        $this->assertEquals('B', $this->object->getClassName());
    }

    /**
     * @covers Koch\Code\Reflection::getClassName
     */
    public function testGetClassName()
    {
        $this->assertEquals('Koch\Code\Reflection', $this->object->getClassName());
    }

    /**
     * @covers Koch\Code\Reflection::getMethods
     * @covers Koch\Code\Reflection::__construct
     */
    public function testGetMethods()
    {
        $methodsArray = $this->object->getMethods();
        $this->assertArrayHasKey('Koch\Code\Reflection', $methodsArray);
        $this->assertArrayHasKey('getMethods', array_flip($methodsArray['Koch\Code\Reflection']));

        // class does not exist exception
        $this->object = new Reflection('A');
        $this->setExpectedException('RuntimeException', 'Class not existing: A');
        $methodsArray = $this->object->getMethods();
    }
}
