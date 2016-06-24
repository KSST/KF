<?php

namespace KochTest\Tests;

use Koch\Tests\DoctrineTestCase;

class DoctrineTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineTestCase
     */
    protected $object;

    public function setUp()
    {
        $this->object = new DoctrineTestCase();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Tests\DoctrineTestCase::setUp
     * @covers Koch\Tests\DoctrineTestCase::getEntityManager
     */
    public function testSetUp()
    {
        $this->object->setUp();
        $em = $this->object->getEntityManager();
        $this->assertTrue(is_object($em));
        //$this->assertInstanceOf('\Doctrine\ORM\EntityManager', $em);
    }
}
