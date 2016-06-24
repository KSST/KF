<?php

namespace KochTest\Cache\Adapter;

use Koch\Cache\Adapter\MongoDb;

class MongoDbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MongoDb
     */
    protected $object;

    protected function setUp()
    {
        $this->markTestSkipped('All Mongo Tests are skipped.');

        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('This test requires the PHP extension "mongo".');
        }

        if (version_compare(phpversion('mongo'), '1.3.0', '>=')) {
            $this->markTestSkipped('This test requires the PHP extension "mongo" >= 1.3.0.');
        }

        $options = [
            'database'   => 'test',
            'collection' => 'test',
        ];

        $this->mongo      = $this->getMockMongo();
        $this->collection = $this->getMockMongoCollection();
        $this->db         = $this->getMockMongoDB();

        $this->db->expects($this->any())
            ->method('selectCollection')->with($options['collection'])
            ->will($this->returnValue($this->collection));

        $this->mongo->expects($this->any())
            ->method('selectDB')->with($options['database'])
            ->will($this->returnValue($this->db));

        // @todo mock injection via options.. omg
        $options['mock'] = ['mongo' => $this->mongo, 'mongodb' => $this->db];

        $this->object = new MongoDb($options);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    public static function SetOptionDataprovider()
    {
        return [
            ['database', 'default'],
            ['collection', 'default'],
        ];
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::setOption
     * @dataProvider SetOptionDataprovider
     */
    public function testSetOption($key, $value)
    {
        $this->object->setOption($key, $value);
        $this->assertEquals($this->object->options[$key], $value);
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::initialize
     */
    public function testInitialize()
    {
        // instant return
        $this->object->collection = new \stdClass();
        $this->assertNull($this->object->initialize());
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::initialize
     * @expectedException RuntimeException
     * @expectedExcetptionMessage The option "collection" must be set.
     */
    public function testInitializeThrowsExceptionCollection()
    {
        $this->object->collection = null;
        $this->object->options    = null;
        $this->object->initialize();
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::initialize
     * @expectedException RuntimeException
     * @expectedExcetptionMessage The option "database" must be set.
     */
    public function testInitializeThrowsExceptionDatabase()
    {
        $this->object->collection = null;
        $this->object->options    = null;
        $this->object->initialize();
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::contains
     * @covers Koch\Cache\Adapter\MongoDb::delete
     */
    public function testDelete()
    {
        $this->collection->expects($this->once())
            ->method('remove')
            ->with($this->equalTo(['key' => 'key1']))
            ->will($this->returnValue(false));

        // assert that, key does not exist before
        $this->assertFalse($this->object->delete('key1'));
        $this->assertFalse($this->object->contains('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::store
     * @covers Koch\Cache\Adapter\MongoDb::delete
     */
    public function testStoreWithoutTTL()
    {
        $this->collection->expects($this->once())->method('remove');
        $this->collection->expects($this->once())->method('insert')->will($this->returnValue(false));

        // assert that, it's not possible to add key with value without a TTL
        $this->assertFalse($this->object->store('key1', 'value1'));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::store
     * @covers Koch\Cache\Adapter\MongoDb::delete
     */
    public function testStoreWithTTL()
    {
        $this->collection->expects($this->once())->method('remove');
        $this->collection->expects($this->once())->method('insert')->will($this->returnValue(true));

        // assert that, we can add key with value with ttl
        $this->assertTrue($this->object->store('key1', 'value1', 120));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::fetch
     */
    public function testFetch()
    {
        $key   = 'keyname';
        $value = 'somevalue';
        $ttl   = 900;

        $item = [
            'key'   => $key,
            'value' => $value,
            'ttl'   => time() + $ttl,
        ];

        $this->collection
            ->expects($this->once())
            ->method('findOne')
            ->with($this->equalTo(['key' => $key]))
            ->will($this->returnValue($item));

        // assert that, we can get that value by key
        $this->assertEquals($value, $this->object->fetch($key));

        // assert that, we can check, if such a key is set
        //$this->assertTrue($this->object->contains('key1'));
        // assert that, we can delete the key
        //$this->assertTrue($this->object->delete('key1'));
        // assert that, we can check that the key is gone
        //$this->assertFalse($this->object->contains('key1'));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::clear
     */
    public function testClear()
    {
        $this->collection->expects($this->once())->method('drop')->will($this->returnValue(true));

        // assert that, clearing the whole cache works
        $this->assertTrue($this->object->clear());
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::getData
     */
    public function testGetData()
    {
        $key   = 'keyname';
        $value = 'somevalue';
        $ttl   = 900;

        $item = [
            'key'   => $key,
            'value' => $value,
            'ttl'   => time() + $ttl,
        ];

        $this->collection
            ->expects($this->once())
            ->method('findOne')
            ->with($this->equalTo(['key' => $key]))
            ->will($this->returnValue($item));

        $this->assertEquals($value, $this->object->getData($key));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::getData
     */
    public function testGetDataTTLHasEnded()
    {
        $key   = 'keyname';
        $value = 'somevalue';
        $ttl   = -10000;

        $item = [
            'key'   => $key,
            'value' => $value,
            'ttl'   => time() + $ttl,
        ];

        $this->collection
            ->expects($this->once())
            ->method('findOne')
            ->with($this->equalTo(['key' => $key]))
            ->will($this->returnValue($item));

        $this->assertEquals(false, $this->object->getData($key));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::getData
     */
    public function testGetDataKeyNotFound()
    {
        $key = 'no-existant-key';

        $this->collection
            ->expects($this->once())
            ->method('findOne')
            ->with($this->equalTo(['key' => $key]))
            ->will($this->returnValue(false));

        $this->assertEquals(false, $this->object->getData($key));
    }

    /**
     * @covers Koch\Cache\Adapter\MongoDb::stats
     */
    public function testStats()
    {
        $this->assertEquals([], $this->object->stats());
    }

    /**
     * Mock the Mongo!
     */
    private function getMockMongo()
    {
        return $this->getMock('Mongo', [], [], '', false, false);
    }

    private function getMockMongoClient()
    {
        return $this->getMockBuilder('MongoClient')
                ->disableOriginalConstructor()
                ->getMock();
    }

    private function getMockMongoDB()
    {
        return $this->getMock('MongoDB', [], [], '', false, false);
    }

    private function getMockMongoCollection()
    {
        return $this->getMock('MongoCollection', [], [], '', false, false);
    }
}
