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
     */
    public function testGetModulePath()
    {
        $this->assertEquals('/Modules/ModuleABC/', $this->object->getModulePath('ModuleABC'));
    }

    /**
     * @covers Koch\Mvc\Mapper::mapControllerToFilename
     */
    public function testMapControllerToFilename()
    {
        $module_path = '/Modules/ModuleA';
        $this->assertEquals(
            '/Modules/ModuleAController/Controller.php',
            $this->object->mapControllerToFilename($module_path)
        );

        $module_path = '/Modules/ModuleB';
        $controller = 'Admin';
        $this->assertEquals(
            '/Modules/ModuleBController/AdminController.php',
            $this->object->mapControllerToFilename($module_path, $controller)
        );
    }

    /**
     * @covers Koch\Mvc\Mapper::mapControllerToClassname
     */
    public function testMapControllerToClassname()
    {
        $module = 'SomeModuleA';
        $this->assertEquals(
            '__NAMESPACE__\Modules\SomeModuleA\Controller\SomeModuleAController',
            $this->object->mapControllerToClassname($module)
        );

        $module = 'SomeModuleB';
        $controller = 'SomeControllerA';
        $this->assertEquals(
            '__NAMESPACE__\Modules\SomeModuleB\Controller\SomeControllerAController',
            $this->object->mapControllerToClassname($module, $controller)
        );
    }

    /**
     * @covers Koch\Mvc\Mapper::mapActionToMethodname
     */
    public function testMapActionToMethodname()
    {
        // default action
        $this->assertEquals('actionIndex', $this->object->mapActionToMethodname());

        // custom action
        $this->assertEquals('actionSomeActionName', $this->object->mapActionToMethodname('someActionName'));
    }
}
