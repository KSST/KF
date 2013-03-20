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
        $this->adapter = $this->getMock('Koch\Pagination\AdapterInterface');

        // constructor injection
        $this->object = new Pagination($this->adapter);
    }

    protected function tearDown()
    {
        $this->object;
    }

    /**
     * @covers Koch\Pagination\Pagination::setAdapter
     */
    public function testSetAdapter()
    {
        // setter injection
        $r = $this->object->setAdapter($this->adapter);

        // fluent
        $this->assertInstanceOf('Koch\Pagination\Pagination', $r);
    }

    /**
     * @covers Koch\Pagination\Pagination::getAdapter
     */
    public function testGetAdapter()
    {
        $this->assertSame($this->adapter, $this->object->getAdapter());
    }

    /**
     * @covers Koch\Pagination\Pagination::setMaxResultsPerPage
     * @covers Koch\Pagination\Pagination::getMaxResultsPerPage
     */
    public function testSetMaxResultsPerPage()
    {
        $maxResultsPerPage = '15';
        $r = $this->object->setMaxResultsPerPage($maxResultsPerPage);

        $this->assertEquals($this->object->getMaxResultsPerPage(), $maxResultsPerPage);

        // fluent
        $this->assertInstanceOf('Koch\Pagination\Pagination', $r);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There must be more than 1 MaxResultsPerPage.
     */
    public function testSetMaxResultsPerPage_throwsException()
    {
        $this->object->setMaxResultsPerPage(-10);
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

     /**
      * @covers Koch\Pagination\Pagination::getTotalNumberOfResults
      */
    public function getTotalNumberOfResults()
    {
        // hmm, phpunit bug? calls to the mock are not covered
        unset($this->object->totalNumberOfResults);
        $this->assertNull($this->object->totalNumberOfResults);

        $this->adapter->expects($this->any())
            ->method('getTotalNumberOfResults')->will($this->returnValue(666));

        $this->assertSame(666, $this->object->getTotalNumberOfResults());
    }

    /**
     * @covers Koch\Pagination\Pagination::setMaxResultsPerPage
     * @covers Koch\Pagination\Pagination::haveToPaginate
     */
    public function testHaveToPaginate()
    {
        $this->adapter->expects($this->any())
            ->method('getTotalNumberOfResults')->will($this->returnValue(15));

        $this->object->setMaxResultsPerPage(16);
        $this->assertFalse($this->object->haveToPaginate());
        $this->object->setMaxResultsPerPage(15);
        $this->assertFalse($this->object->haveToPaginate());
        $this->object->setMaxResultsPerPage(14);
        $this->assertTrue($this->object->haveToPaginate());
        $this->object->setMaxResultsPerPage(1);
        $this->assertTrue($this->object->haveToPaginate());
    }

    /**
     * @covers Koch\Pagination\Pagination::getTotalNumberOfResults
     * @covers Koch\Pagination\Pagination::setTotalNumberOfResults
     */
    public function testSetTotalNumberOfResults()
    {
        $this->adapter->expects($this->never())->method('getTotalNumberOfResults');

        $this->object->setTotalNumberOfResults(2);
        $this->assertEquals(2, $this->object->getTotalNumberOfResults());
    }

    /**
     * @covers Koch\Pagination\Pagination::getNumberOfPages
     * @covers Koch\Pagination\Pagination::setMaxResultsPerPage
     * @covers Koch\Pagination\Pagination::getLastPage
     */
    public function testGetNumberOfPages()
    {
        $this->adapter->expects($this->any())
            ->method('getTotalNumberOfResults')->will($this->returnValue(100));

        $this->object->setMaxResultsPerPage(10);
        $this->assertSame(10, $this->object->getNumberOfPages());
        $this->assertSame(10, $this->object->getLastPage());
    }

    /**
     * @covers Koch\Pagination\Pagination::setMaxResultsPerPage
     * @covers Koch\Pagination\Pagination::getCurrentPageResults
     * @covers Koch\Pagination\Pagination::getCurrentPage
     */
    public function testGetCurrentPageResults()
    {
        $returnValues = array(
            array('foo' => 'bar', 'bar' => 'foo'),
            array('fanta', 'reiner', 'kristall', 'weizen'),
        );

        $this->adapter->expects($this->once())->method('getSlice')
            ->with($this->equalTo(20), $this->equalTo(10))
            ->will($this->returnValue($returnValues[0]));

        $this->object->setMaxResultsPerPage(10);
        $this->object->setCurrentPage(3, true);
        $this->assertSame($returnValues[0], $this->object->getCurrentPageResults());

        // cached
        $this->assertSame($returnValues[0], $this->object->getCurrentPageResults());
    }

    /**
     * @covers Koch\Pagination\Pagination::setMaxResultsPerPage
     * @covers Koch\Pagination\Pagination::getCurrentPage
     * @covers Koch\Pagination\Pagination::hasPreviousPage
     * @covers Koch\Pagination\Pagination::getPreviousPage
     * @covers Koch\Pagination\Pagination::hasNextPage
     * @covers Koch\Pagination\Pagination::getNextPage
     */
    public function testGetPreviousPage()
    {
         $this->adapter->expects($this->any())
            ->method('getTotalNumberOfResults')->will($this->returnValue(25));

        $this->object->setMaxResultsPerPage(5);
        $this->object->setCurrentPage(1);

        // first page does not have a previous page
        $this->assertFalse($this->object->hasPreviousPage());
        try {
            $this->object->getPreviousPage();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\LogicException', $e);
        }

        // first page has a next page
        $this->assertTrue($this->object->hasNextPage());
        $this->assertSame(2, $this->object->getNextPage());

        // check in between
        $this->object->setCurrentPage(5);
        $this->assertTrue($this->object->hasPreviousPage());
        $this->assertSame(4, $this->object->getPreviousPage());

        // last page
        $this->object->setCurrentPage(25);
        $this->assertTrue($this->object->hasPreviousPage());
        $this->assertSame(24, $this->object->getPreviousPage());
        $this->assertFalse($this->object->hasNextPage());

        // last page does not have a next page
        try {
            $this->object->getNextPage();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\LogicException', $e);
        }
    }

    /**
     * @covers Koch\Pagination\Pagination::render
     * @covers Koch\Pagination\Pagination::setAdapter
     * @covers Koch\Pagination\Pagination::setMaxResultsPerPage
     */
    public function testRender()
    {
        // dataset
        $this->array = array();
        for ($i = 0; $i < 10; $i++) {
            $this->array[] = rand(1, 999);
        }

        // dataset adapter
        $adapter = new \Koch\Pagination\Adapter\NativeArray($this->array);
        $this->object->setAdapter($adapter);

        // settings
        $this->object->setMaxResultsPerPage(10);

        // expected pagination
        $expected = '<nav class="pagination">';
        $expected .= '<a href="URL">&lsaquo;&nbsp;First</a>';
        $expected .= '<a href="URL">1</a>';
        $expected .= '<a href="URL">&gt;</a>';
        $expected .= '<a href="URL">&nbsp;Last&rsaquo;</a>';
        $expected .= '</nav>';

        $this->assertEquals($expected, $this->object->render());
    }
}
