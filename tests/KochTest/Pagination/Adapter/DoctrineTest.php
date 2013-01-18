<?php
namespace KochTest\Pagination;

class DoctrineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Doctrine
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Doctrine;
    }

    protected function tearDown()
    {
    }
}
