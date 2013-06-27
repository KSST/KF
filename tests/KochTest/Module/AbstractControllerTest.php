<?php

namespace KochTest\Module;

class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var object Controller instanceOf Koch\Module\AbstractContoller
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $request = new \Koch\Http\HttpRequest;
        $response = new \Koch\Http\HttpResponse;

        // we can't test abstract base classes directly, so we test against a child
        $this->object = new \KochTest\Fixtures\Application\Modules\News\Controller\NewsController($request, $response);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    public function testGetEntityNameFromClassname()
    {
        $entity = $this->object->getEntityNameFromClassname();

        $this->assertEquals('Entity\News', $entity);
    }

}