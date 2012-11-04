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
    }

    /**
     * @covers Koch\Router\TargetRoute::instantiate
     * @todo   Implement testInstantiate().
     */
    public function testInstantiate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::setFilename
     * @todo   Implement testSetFilename().
     */
    public function testSetFilename()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getFilename
     * @todo   Implement testGetFilename().
     */
    public function testGetFilename()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::setClassname
     * @todo   Implement testSetClassname().
     */
    public function testSetClassname()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getClassname
     * @todo   Implement testGetClassname().
     */
    public function testGetClassname()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::setController
     * @todo   Implement testSetController().
     */
    public function testSetController()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
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
     * @todo   Implement testSetAction().
     */
    public function testSetAction()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getAction
     * @todo   Implement testGetAction().
     */
    public function testGetAction()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getActionNameWithoutPrefix
     * @todo   Implement testGetActionNameWithoutPrefix().
     */
    public function testGetActionNameWithoutPrefix()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::setId
     * @covers Koch\Router\TargetRoute::getId
     */
    public function testSetId()
    {
        $this->assertEquals($this->object->getId(), $this->object->setId('1'));
    }

    /**
     * @covers Koch\Router\TargetRoute::setAction
     * @covers Koch\Router\TargetRoute::getActionName
     * @covers Koch\Router\TargetRoute::getMethod
     */
    public function testGetActionName()
    {
        $this->object->setAction('MyAction');

        $this->assertEquals('action_MyAction', $this->object->getMethod());
        $this->assertEquals('action_MyAction', $this->object->getActionName());
    }

    /**
     * @covers Koch\Router\TargetRoute::setMethod
     * @covers Koch\Router\TargetRoute::getMethod
     * @covers Koch\Router\TargetRoute::getActionName
     */
    public function testSetMethod()
    {
        $this->assertEquals('action_list', $this->object->getMethod());

        $this->object->setMethod('a-method');
        $this->assertEquals('a-method', $this->object->getActionName());
    }

    /**
     * @covers Koch\Router\TargetRoute::getMethod
     * @todo   Implement testGetMethod().
     */
    public function testGetMethod()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
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
     * @todo   Implement testGetParameters().
     */
    public function testGetParameters()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getFormat
     * @todo   Implement testGetFormat().
     */
    public function testGetFormat()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getRequestMethod
     * @todo   Implement testGetRequestMethod().
     */
    public function testGetRequestMethod()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
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
     * @covers Koch\Router\TargetRoute::setThemeName
     * @todo   Implement testSetThemeName().
     */
    public function testSetThemeName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getModRewriteStatus
     * @todo   Implement testGetModRewriteStatus().
     */
    public function testGetModRewriteStatus()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::dispatchable
     * @todo   Implement testDispatchable().
     */
    public function testDispatchable()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::setSegmentsToTargetRoute
     * @todo   Implement testSetSegmentsToTargetRoute().
     */
    public function testSetSegmentsToTargetRoute()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::reset
     * @todo   Implement testReset().
     */
    public function testReset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::getRoute
     * @todo   Implement testGetRoute().
     */
    public function testGetRoute()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::debug
     * @todo   Implement testDebug().
     */
    public function testDebug()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\Router\TargetRoute::toArray
     * @todo   Implement testToArray().
     */
    public function testToArray()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
