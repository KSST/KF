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
     * @covers Koch\Cache\AbstractCache::prefixKey
     */
    public function testApplyPrefix()
    {
        $this->object->setPrefix('newPrefix');

        $key = 'Key';
        $this->assertEquals('newPrefixKey', $this->object->prefixKey($key));
    }
}
