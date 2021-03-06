<?php

namespace KochTest\Pagination\Adapter;

use Koch\Pagination\Adapter\NativeArray;

class NativeArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NativeArray
     */
    protected $object;

    protected $array = [];

    protected function setUp()
    {
        $this->array = [];
        for ($i = 0; $i < 10; ++$i) {
            $this->array[] = rand(1, 999);
        }

        $this->object = new NativeArray($this->array);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers Koch\Pagination\Adapter\NativeArray::getArray
     */
    public function testGetArray()
    {
        $this->assertSame($this->array, $this->object->getArray());
    }

    /**
     * @covers Koch\Pagination\Adapter\NativeArray::getTotalNumberOfResults
     */
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
        return [
            [5, 10],
            [10, 5],
        ];
    }
}
