<?php
namespace Koch\Router;

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
       $e = 'abc';
       $this->object->setFilename($e);
       $this->assertEquals($e, $this->object->getFilename());
    }

    /**
     * @covers Koch\Router\TargetRoute::setClassname
     * @covers Koch\Router\TargetRoute::getClassname
     */
    public function testSetClassname()
    {
        $e = 'abc';
        $this->object->setClassname($e);
        $this->assertEquals($e, $this->object->getClassname());
    }

    /**
     * @covers Koch\Router\TargetRoute::getController
     * @covers Koch\Router\TargetRoute::setController
     */
    public function testGetController()
    {
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
    }

    /**
     * @covers Koch\Router\TargetRoute::getParameters
     */
    public function testGetParameters()
    {
       $er = $this->object->getParameters();
       $this->assertTrue(is_array($er));
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
       $er = $this->object->getRequestMethod();
       $this->assertEquals($er, 'GET');
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
       $this->assertEquals('admin', $this->object->getBackendTheme());

       $theme = 'A-Backend-Theme';
       $_SESSION['user']['backend_theme'] = $theme;
       $this->assertEquals($theme, $this->object->getBackendTheme());
    }

    /**
     * @covers Koch\Router\TargetRoute::getFrontendTheme
     */
    public function testGetFrontendTheme()
    {
        $this->assertEquals('standard', $this->object->getFrontendTheme());

        $theme = 'A-Frontend-Theme';
        $_SESSION['user']['frontend_theme'] = $theme;
        $this->assertEquals($theme, $this->object->getFrontendTheme());
    }

    /**
     * @covers Koch\Router\TargetRoute::getThemeName
     */
    public function testGetThemeName()
    {
        // default
        // NOTICE! this assertion needs a TargetRoute and a $_Session reset
        $this->assertEquals('standard', $this->object->getThemeName());

        // explicitly set
        $theme = 'MyTheme';
        $this->object->setThemeName($theme);
        $this->assertEquals($theme, $this->object->getThemeName());
        $this->object->reset();

        $this->object->setModule('controlcenter');
        $this->assertEquals('admin', $this->object->getThemeName());

        $this->object->setController('NewsAdminController');
        $this->assertEquals('admin', $this->object->getThemeName());
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
     * @todo   Implement testDispatchable().
     */
    public function testDispatchable()
    {
        // for now assertFalse :)
        $this->assertFalse($this->object->dispatchable());
    }

    /**
     * @covers Koch\Router\TargetRoute::setSegmentsToTargetRoute
     * @todo   Implement testSetSegmentsToTargetRoute().
     */
    public function testSetSegmentsToTargetRoute()
    {
        $segments = array();
        $er = $this->object->setSegmentsToTargetRoute($segments);

        $this->assertTrue(is_object($er));
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
        $targetRouteParameters_AFTER = $this->object->getRoute();
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

    /**
     * @covers Koch\Router\TargetRoute::toArray
     */
    public function testToArray()
    {
        $er = $this->object->toArray();
        $this->assertTrue(is_array($er));
    }
}
