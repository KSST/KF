<?php
namespace KochTest\Pagination;

use \Koch\Pagination\Adapter\Doctrine;
use \Koch\PHPUnit\DoctrineTestCase;

// doctrine/data-fixtures
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class DoctrineTest extends DoctrineTestCase
{
    /**
     * @var \Koch\Pagination\Adapter\DoctrineCollection
     */
    protected $object;

    public $collection;

    public function setUp()
    {
        parent::setUp();

        // Setup Doctrine2 fixtures
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/../../../KochTest/Fixtures/Pagination');

        // execute fixtures
        $fixtures = $loader->getFixtures();
        $em = $this->getEntityManager();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures);

        $this->object = new Doctrine($this->collection);
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
            ->will($this->returnValue(120));

        $this->assertSame(120, $this->object->getTotalNumberOfResults());
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
