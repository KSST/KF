<?php

namespace KochTest\Debug;

use Koch\PHPUnit\DoctrineTestCase;

class DoctrineTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineTestCase
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new DoctrineTestCase;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\PHPUnit\DoctrineTestCase::setUp
     * @covers Koch\PHPUnit\DoctrineTestCase::getEntityManager
     */
    public function testSetUp()
    {
        $this->object->setUp();
        $em = $this->object->getEntityManager();
        $this->assertTrue(is_object($em));
        //$this->assertInstanceOf('\Doctrine\ORM\EntityManager', $em);
    }
}

