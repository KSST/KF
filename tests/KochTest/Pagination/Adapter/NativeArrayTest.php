<?php
namespace KochTest\Pagination\Adapter;

use Koch\Pagination\Adapter\NativeArray;

class NativeArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NativeArray
     */
    protected $object;

    protected $array = array();

    protected function setUp()
    {
        $this->array = array();
        for ($i = 0; $i < 10; $i++) {
            $this->array[] = rand(1, 999);
        }

        $this->object = new NativeArray($this->array);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    public function testGetArray()
    {
        $this->assertSame($this->array, $this->object->getArray());
    }

    public function testGetTotalNumberOfResults()
    {
        $this->assertSame(count($this->array), $this->object->getTotalNumberOfResults());
    }

    /**
     * @dataProvider dataProviderGetResults
     */
    public function testGetResults($offset, $length)
    {
        $this->assertSame(
            array_slice($this->array, $offset, $length),
            $this->object->getSlice($offset, $length)
        );
    }

    public function dataProviderGetResults()
    {
        return array(
            array(5, 10),
            array(10, 5),
        );
    }
}
