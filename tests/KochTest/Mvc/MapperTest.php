<?php
namespace Koch\Mvc;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Mapper;
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
     * @covers Koch\Mvc\Mapper::getModulePath
     * @todo   Implement testGetModulePath().
     */
    public function testGetModulePath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Mvc\Mapper::mapControllerToFilename
     * @todo   Implement testMapControllerToFilename().
     */
    public function testMapControllerToFilename()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Mvc\Mapper::mapControllerToClassname
     */
    public function testMapControllerToClassname()
    {
        $module = 'SomeModuleA';
        $this->assertEquals(
            'Clansuite\Modules\SomeModuleA\Controller\SomeModuleAController',
            $this->object->mapControllerToClassname($module)
        );

        $module = 'SomeModuleB';
        $controller = 'SomeControllerA';
        $this->assertEquals(
            'Clansuite\Modules\SomeModuleB\Controller\SomeControllerAController',
            $this->object->mapControllerToClassname($module, $controller)
        );
    }

    /**
     * @covers Koch\Mvc\Mapper::mapActionToMethodname
     */
    public function testMapActionToMethodname()
    {
        // default action
        $this->assertEquals('action_index', $this->object->mapActionToMethodname());

        // custom action
        $this->assertEquals('action_someActionName', $this->object->mapActionToMethodname('someActionName'));
    }
}
