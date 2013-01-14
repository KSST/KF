<?php
namespace KochTest\Pagination;

use Koch\Pagination\Pagination;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pagination
     */
    protected $object;
    protected $adapter;

    protected function setUp()
    {
        // testing: mock adapter from interface
        $this->adapter = $this->getMock('Koch\Pagination\AdapterInterface');

        $this->object = new Pagination($this->adapter);
    }

    protected function tearDown()
    {
        $this->object;
    }

    public function testSetAdapter()
    {
        $r = $this->object->setAdapter($this->adapter);

        // fluent
        $this->assertInstanceOf('Koch\Pagination\Pagination', $r);
    }

    public function testGetAdapter()
    {
        $this->assertSame($this->adapter, $this->object->getAdapter());
    }

    /**
     * @covers Koch\Pagination\Pagination::setMaxItemsPerPage
     * @covers Koch\Pagination\Pagination::getMaxItemsPerPage
     */
    public function testSetMaxItemsPerPage()
    {
        $maxItemsPerPage = '15';
        $r = $this->object->setMaxItemsPerPage($maxItemsPerPage);

        $this->assertEquals($this->object->getMaxItemsPerPage(), $maxItemsPerPage);

        // fluent
        $this->assertInstanceOf('Koch\Pagination\Pagination', $r);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There must be 1 or more MaxItemsPerPage.
     */
    public function testSetMaxItemsPerPage_throwsException()
    {
        $this->object->setMaxItemsPerPage(-10);
    }

     /**
     * @covers Koch\Pagination\Pagination::setCurrentPage
     * @covers Koch\Pagination\Pagination::getCurrentPage
     */
    public function testSetCurrentPage()
    {
        // string
        $currentPage = '15';
        $this->object->setCurrentPage($currentPage);
        $this->assertEquals($this->object->getCurrentPage(), $currentPage);

        // int
        $currentPage = 15;
        $r = $this->object->setCurrentPage($currentPage);
        $this->assertEquals($this->object->getCurrentPage(), $currentPage);

        // fluent
        $this->assertInstanceOf('Koch\Pagination\Pagination', $r);
    }
}
