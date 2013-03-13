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

    public function testGetTotalNumberOfResults()
    {
        $dql = "SELECT u FROM KochTest\Doctrine\Entity\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new Doctrine($query);
        $this->assertEquals(2, $adapter->getTotalNumberOfResults());
    }
}
