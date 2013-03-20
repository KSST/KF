<?php
namespace KochTest\Mvc;

use Koch\Mvc\Mapper;

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
     * @covers Koch\Mvc\Mapper::setApplicationNamespace
     * @covers Koch\Mvc\Mapper::getApplicationNamespace
     */
    public function testSetApplicationNamespace()
    {
        $this->object->setApplicationNamespace(__NAMESPACE__);
        $this->assertEquals('\KochTest\Mvc', Mapper::$applicationNamespace);
        $this->assertEquals('\KochTest\Mvc', $this->object->getApplicationNamespace());
    }

    /**
     * @covers Koch\Mvc\Mapper::getModulePath
     */
    public function testGetModulePath()
    {
        $this->assertEquals(APPLICATION_MODULES_PATH . 'ModuleABC/', $this->object->getModulePath('ModuleABC'));
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
        $this->object->setApplicationNamespace('Application');

        $module = 'SomeModuleA';
        $this->assertEquals(
            '\Application\Modules\SomeModuleA\Controller\SomeModuleAController',
            $this->object->mapControllerToClassname($module)
        );

        $module = 'SomeModuleB';
        $controller = 'SomeControllerA';
        $this->assertEquals(
            '\Application\Modules\SomeModuleB\Controller\SomeControllerAController',
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
