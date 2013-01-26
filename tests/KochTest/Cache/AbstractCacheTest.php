<?php

namespace KochTest\Cache;

use Koch\Cache\Adapter\File;

class AbstractCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCache
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        // we are using the cache adapter File here,
        // it's a class extending the abstract class
        // abstract classes cannot be instantiated
        $this->object = new File;
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
     * @covers Koch\Cache\AbstractCache::__construct
     */
    public function testConstructor()
    {
        unset($this->object);

        $options = array('prefix' => 'value');
        $this->object = new File($options);

        $this->assertEquals('value', $this->object->options['prefix']);
    }

    /**
     * @covers Koch\Cache\AbstractCache::setOptions
     * @covers Koch\Cache\AbstractCache::setOption
     */
    public function testSetOptions()
    {
        $options = array(
             'prefix' => 'someprefix',
             'ttl' => 1337
        );
        $this->object->setOptions($options);

        $this->assertEquals('someprefix', $this->object->options['prefix']);
        $this->assertEquals(1337, $this->object->options['ttl']);
    }

    public static function SetOptionDataprovider()
    {
        return array(
          array('prefix', 'some-prefix'),
          array('ttl', 1337)
        );
    }

    /**
     * @covers Koch\Cache\AbstractCache::setOption
     * @dataProvider SetOptionDataprovider
     */
    public function testSetOption($key, $value)
    {
        $this->object->setOption($key, $value);
        $this->assertEquals($this->object->options[$key], $value);
    }

    public static function SetOptionDataproviderInvalidData()
    {
        return array(
          array('prefix', ''),
          array('ttl', -1),
          array('unknown', 'unknown')
        );
    }

    /**
     * @covers Koch\Cache\AbstractCache::setOption
     * @covers Koch\Cache\AbstractCache::setOptions
     * @dataProvider SetOptionDataproviderInvalidData
     * @expectedException InvalidArgumentException
     */
    public function testSetOption_throwsException($key, $value)
    {
        $this->object->setOption($key, $value);
    }

    /**
     * @covers Koch\Cache\AbstractCache::setPrefix
     * @covers Koch\Cache\AbstractCache::getPrefix
     */
    public function testSetPrefix()
    {
        $prefix = 'newPrefix';
        $this->object->setPrefix($prefix);
        $this->assertEquals($prefix, $this->object->getPrefix());
    }

    /**
     * @covers Koch\Cache\AbstractCache::setPrefix
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Prefix must not be empty.
     */
    public function testSetPrefix_throwsException()
    {
        $this->object->setPrefix('');
    }

    /**
     * @covers Koch\Cache\AbstractCache::prefixKey
     */
    public function testApplyPrefix()
    {
        $this->object->setPrefix('newPrefix');

        $key = 'Key';
        $this->assertEquals('newPrefixKey', $this->object->prefixKey($key));
    }

    /**
     * @covers Koch\Cache\AbstractCache::__set
     * @covers Koch\Cache\AbstractCache::__isset
     * @covers Koch\Cache\AbstractCache::__get
     * @covers Koch\Cache\AbstractCache::__unset
     */
    public function testSet()
    {
        // set
        $this->object->key = 'value';
        // isset
        $this->assertTrue(isset($this->object->key));
        // get
        $this->assertEquals('value', $this->object->key);
        // unset
        unset($this->object->key);
        $this->assertFalse(isset($this->object->key));
    }
}
