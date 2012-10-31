<?php

namespace KochTest\Registry;

use Koch\Registry\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Registry
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Registry;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Koch\Mvc\Registry::set
     */
    public function testSet()
    {
        // set object to registry
        $a = new \stdClass;
        $this->object->set('A', $a);

        $this->assertTrue($this->object->has('A'));

        // set classname to registry
        $this->object->set('B', 'B');
        $this->assertTrue($this->object->has('B'));

        // set closure (as a resolver) to registry
        $closure = function() {
            return new \stdClass;
        };
        $this->object->set('C', $closure);

        $this->assertTrue($this->object->has('C'));
    }

    /**
     * @covers Koch\Mvc\Registry::has
     */
    public function testHas()
    {
        // set object to registry
        $a = new \stdClass;
        $this->object->set('A', $a);

        $this->assertTrue($this->object->has('A'));
    }

    /**
     * @covers Koch\Mvc\Registry::get
     */
    public function testGet()
    {
        // set object to registry
        $a = new \stdClass;
        $this->object->set('A', $a);

        $resultA = $this->object->get('A');

        $this->assertInstanceOf('StdClass', $resultA);


        // set classname to registry
        $this->object->set('B', 'KochTest\Registry\B');

        $resultB = $this->object->get('B');

        $this->assertInstanceOf('KochTest\Registry\B', $resultB);

        // set closure (as a resolver) to registry
        $closure = function() {
            return new \stdClass;
        };
        $this->object->set('C', $closure);

        $resultC = $this->object->get('C');

        $this->assertInstanceOf('StdClass', $resultC);
    }
}

class B {

}
