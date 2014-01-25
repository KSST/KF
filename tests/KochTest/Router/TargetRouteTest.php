<?php

namespace KochTest\Router;

use Koch\Router\TargetRoute;

class TargetRouteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TargetRoute
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = TargetRoute::instantiate();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        // static instance
        $this->object->reset();
        unset($_SESSION);
    }

    /**
     * @covers Koch\Router\TargetRoute::instantiate
     */
    public function testInstantiate()
    {
        // Note: phpunit BUG
        // $this->assertInstanceOf() autoloads the class, leading to redeclaration error

        $tr = TargetRoute::instantiate();
        $this->assertTrue($tr instanceof TargetRoute);
    }

    /**
     * @covers Koch\Router\TargetRoute::setFilename
     * @covers Koch\Router\TargetRoute::getFilename
     */
    public function testSetFilename()
    {
        //$this->object->setApplicationNamespace('KochTest/Fixtures/Application');
        // test filename construction, if empty filename
        $file1 = '';
        $this->object->setFilename($file1);
        $this->assertEquals(
            realpath(APPLICATION_MODULES_PATH . 'Index/Controller/IndexController.php'),
            $this->object->getFilename()
        );

        // setter test
        $file2 = 'abc';
        $this->object->setFilename($file2);
        $this->assertEquals($file2, $this->object->getFilename());
    }

    /**
     * @covers Koch\Router\TargetRoute::setClassname
     * @covers Koch\Router\TargetRoute::getClassname
     */
    public function testSetClassname()
    {
        $this->object->setApplicationNamespace('\KochTest\Fixtures\Application');

        // test filename construction, if empty filename
        $class1 = '';
        $this->object->setClassname($class1);
        $this->assertEquals(
            '\KochTest\Fixtures\Application\Modules\Index\Controller\IndexController',
            $this->object->getClassname()
        );

        $class2 = 'abc';
        $this->object->setClassname($class2);
        $this->assertEquals($class2, $this->object->getClassname());
    }

    /**
     * @covers Koch\Router\TargetRoute::getController
     * @covers Koch\Router\TargetRoute::setController
     */
    public function testGetController()
    {
        $class = '';
        $this->object->setController($class);
        // if controller not set, it will be the default module name
        $this->assertEquals('Index', $this->object->getController());

        $this->object->setController('aController');
        $this->assertEquals('AController', $this->object->getController());
    }

    /**
     * @covers Koch\Router\TargetRoute::setModule
     * @covers Koch\Router\TargetRoute::getModule
     */
    public function testSetModule()
    {
        $module = 'ABCModule';
        $this->object->setModule($module);
        $this->assertEquals($module, $this->object->getModule());
    }

    /**
     * @covers Koch\Router\TargetRoute::setAction
     * @covers Koch\Router\TargetRoute::getAction
     */
    public function testSetAction()
    {
        $e = 'abc';
        $this->object->setAction($e);
        $this->assertEquals($e, $this->object->getAction());
    }

    /**
     * @covers Koch\Router\TargetRoute::getActionNameWithoutPrefix
     */
    public function testGetActionNameWithoutPrefix()
    {
        $this->object->setAction('myAction');
        $er = $this->object->getActionNameWithoutPrefix();
        $this->assertEquals($er, 'myAction');
    }

    /**
     * @covers Koch\Router\TargetRoute::setId
     * @covers Koch\Router\TargetRoute::getId
     */
    public function testSetId()
    {
        $id = '1';
        $this->object->setId($id);
        $this->assertEquals($id, $this->object->getId());
    }

    /**
     * @covers Koch\Router\TargetRoute::setAction
     * @covers Koch\Router\TargetRoute::getActionName
     * @covers Koch\Router\TargetRoute::getMethod
     */
    public function testGetActionName()
    {
        $this->object->setAction('MyAction');

        $this->assertEquals('actionMyAction', $this->object->getMethod());
        $this->assertEquals('actionMyAction', $this->object->getActionName());
    }

    /**
     * @covers Koch\Router\TargetRoute::setMethod
     * @covers Koch\Router\TargetRoute::getMethod
     * @covers Koch\Router\TargetRoute::getActionName
     */
    public function testSetMethod()
    {
        $this->assertEquals('actionList', $this->object->getMethod());

        $this->object->setMethod('a-method');
        $this->assertEquals('a-method', $this->object->getActionName());
    }

    /**
     * @covers Koch\Router\TargetRoute::setParameters
     * @covers Koch\Router\TargetRoute::getParameters
     */
    public function testSetParameters()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->object->setParameters(array('param1' => 'p1-value'));

        $this->assertArrayHasKey('param1', $this->object->getParameters());

        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @covers Koch\Router\TargetRoute::getParameters
     */
    public function testGetParameters()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // test for default array
        $er = $this->object->getParameters();
        $this->assertTrue(is_array($er));

        // test if values from $_POST array are automatically populated
        // POST array is not populated on linux???
        /* $_SERVER['REQUEST_METHOD'] = 'POST';
          $_POST['KEY1'] = 'VALUE1';

          $er = $this->object->getParameters();
          $this->assertTrue(is_array($er));
          $this->assertArrayHasKey('KEY1', $er);

          unset($_SERVER['REQUEST_METHOD']);
          unset($_POST['KEY1']); */
    }

    /**
     * @covers Koch\Router\TargetRoute::getFormat
     */
    public function testGetFormat()
    {
        $er = $this->object->getFormat();
        $this->assertEquals($er, 'html');
    }

    /**
     * @covers Koch\Router\TargetRoute::getRequestMethod
     */
    public function testGetRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $er = $this->object->getRequestMethod();
        $this->assertEquals($er, 'GET');

        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @covers Koch\Router\TargetRoute::getLayoutMode
     */
    public function testGetLayoutMode()
    {
        $this->object->setParameters(array('layout' => true));
        $this->assertTrue($this->object->getLayoutMode());
    }

    /**
     * @covers Koch\Router\TargetRoute::getAjaxMode
     * @depend Koch\Http\HttpRequest::isAjax
     */
    public function testGetAjaxMode()
    {
        $this->assertFalse($this->object->getAjaxMode());
    }

    /**
     * @covers Koch\Router\TargetRoute::setRenderEngine
     * @covers Koch\Router\TargetRoute::getRenderEngine
     */
    public function testSetRenderEngine()
    {
        $renderEngine = 'CopyCat';
        $this->object->setRenderEngine($renderEngine);
        $this->assertEquals($renderEngine, $this->object->getRenderEngine());
    }

    /**
     * @covers Koch\Router\TargetRoute::getBackendTheme
     */
    public function testGetBackendTheme()
    {
        $this->assertEquals('default', $this->object->getBackendTheme());

        $theme = 'A-Backend-Theme';
        $_SESSION['user']['backend_theme'] = $theme;
        $this->assertEquals($theme, $this->object->getBackendTheme());
    }

    /**
     * @covers Koch\Router\TargetRoute::getFrontendTheme
     */
    public function testGetFrontendTheme()
    {
        $this->assertEquals('default', $this->object->getFrontendTheme());

        $theme = 'A-Frontend-Theme';
        $_SESSION['user']['frontend_theme'] = $theme;
        $this->assertEquals($theme, $this->object->getFrontendTheme());
    }

    /**
     * @covers Koch\Router\TargetRoute::getThemeName
     * @covers Koch\Router\TargetRoute::setThemeName
     */
    public function testGetThemeName()
    {
        // default
        // NOTICE! this assertion needs a TargetRoute and a $_Session reset
        $this->assertEquals('default', $this->object->getThemeName());

        // explicitly set
        $theme = 'MyTheme';
        $this->object->setThemeName($theme);
        $this->assertEquals($theme, $this->object->getThemeName());
        $this->object->reset();

        $this->object->setModule('controlcenter');
        $this->assertEquals('default', $this->object->getThemeName());

        $this->object->setController('NewsAdminController');
        $this->assertEquals('default', $this->object->getThemeName());
    }

    /**
     * @covers Koch\Router\TargetRoute::getModRewriteStatus
     */
    public function testGetModRewriteStatus()
    {
        $er = $this->object->getModRewriteStatus();
        $this->assertFalse($er);
    }

    /**
     * @covers Koch\Router\TargetRoute::dispatchable
     */
    public function testDispatchable()
    {
        $this->object->setApplicationNamespace('\KochTest\Fixtures\Application');

        $this->assertTrue($this->object->dispatchable());
    }

    /**
     * @covers Koch\Router\TargetRoute::setSegmentsToTargetRoute
     */
    public function testSetSegmentsToTargetRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $segments = array();
        $er       = $this->object->setSegmentsToTargetRoute($segments);

        $this->assertTrue(is_object($er));

        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @covers Koch\Router\TargetRoute::reset
     */
    public function testReset()
    {
        $targetRouteParameters_BEFORE = $this->object->getRoute();
        $this->object->setID('1');
        $targetRouteParameters_MODDED = $this->object->getRoute();
        $this->assertNotEquals($targetRouteParameters_MODDED, $targetRouteParameters_BEFORE);
        $this->object->reset();
        $targetRouteParameters_AFTER  = $this->object->getRoute();
        $this->assertEquals($targetRouteParameters_AFTER, $targetRouteParameters_BEFORE);
    }

    /**
     * @covers Koch\Router\TargetRoute::getRoute
     */
    public function testGetRoute()
    {
        // this fetches the parameters array
        $er = $this->object->getRoute();
        $this->assertTrue(is_array($er));
    }
}
