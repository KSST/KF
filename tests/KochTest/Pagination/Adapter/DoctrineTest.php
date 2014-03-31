<?php
namespace KochTest\Pagination\Adapter;

use \Koch\Pagination\Adapter\Doctrine;
use \Koch\Tests\DoctrineTestCase;

// doctrine/data-fixtures
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class DoctrineTest extends DoctrineTestCase
{
    public function setUp()
    {
        parent::setUp();

        // load doctrine/data-fixtures
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/../../../KochTest/Fixtures/Pagination');
        $fixtures = $loader->getFixtures();

        // execute fixtures
        $em = $this->getEntityManager();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Pagination\Adapter\Doctrine::getTotalNumberOfResults
     */
    public function testGetTotalNumberOfResults()
    {
        $dql = "SELECT u FROM KochTest\Fixtures\Doctrine\Entity\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new Doctrine($query);
        $this->assertEquals(2, $adapter->getTotalNumberOfResults());
    }

    /**
     * @covers Koch\Pagination\Adapter\Doctrine::getSlice
     */
    public function testGetSlice()
    {
        $dql = "SELECT u FROM KochTest\Fixtures\Doctrine\Entity\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new Doctrine($query);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }

    /**
     * @covers Koch\Pagination\Adapter\Doctrine::getQuery
     */
    public function testGetQuery()
    {
        $dql = "SELECT u FROM KochTest\Fixtures\Doctrine\Entity\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new Doctrine($query);
        $this->assertInstanceOf('Doctrine\ORM\Query', $adapter->getQuery());
    }

    /**
     * @covers Koch\Pagination\Adapter\Doctrine::getArray
     */
    public function testGetArray()
    {
        $dql = "SELECT u FROM KochTest\Fixtures\Doctrine\Entity\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new Doctrine($query);
        $this->assertTrue(is_array($adapter->getArray()));
    }
}
