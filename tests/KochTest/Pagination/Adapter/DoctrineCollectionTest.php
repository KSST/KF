<?php
namespace KochTest\Pagination;

use \Koch\Pagination\Adapter\DoctrineCollection;

class DoctrineCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Koch\Pagination\Adapter\DoctrineCollection
     */
    protected $object;

    public $collection;

    protected function setUp()
    {
        if (!interface_exists('Doctrine\Common\Collections\Collection')) {
            $this->markTestSkipped('This test requires Doctrine\Common\Collections\Collection.');
        }

        $this->collection = $this
            ->getMockBuilder('Doctrine\Common\Collections\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new DoctrineCollection($this->collection);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    public function testGetCollection()
    {
        $this->assertSame($this->collection, $this->object->getCollection());
    }

    public function testgetTotalNumberOfResults()
    {
        $this->collection
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(66));

        $this->assertSame(66, $this->object->getTotalNumberOfResults());
    }

    /**
     * @dataProvider getResultsProvider
     */
    public function testGetResults($offset, $length)
    {
        $this->collection
            ->expects($this->once())
            ->method('slice')
            ->with($offset, $length)
            ->will($this->returnValue($all = array(new \DateTime(), new \DateTime())));

        $this->assertSame($all, $this->object->getSlice($offset, $length));
    }

    public function getResultsProvider()
    {
        return array(
            array(3, 8),
            array(3, 6),
        );
    }

    public function testGetArray()
    {
        $this->collection
            ->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue(array('a', 'b')));

        $this->assertEquals(array('a', 'b'), $this->object->getArray());
    }
}