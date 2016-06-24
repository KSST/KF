<?php

namespace KochTest\Config;

use Koch\Config\Config;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class AbstractConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractConfig
     */
    protected $object;

    public function setUp()
    {
        // we are using the config adapter native PHP here,
        // it's a class extending the abstract class
        // abstract classes cannot be instantiated
        $this->object = new Config();

        vfsStreamWrapper::register();
        $this->configFileURL = vfsStream::url('root/test.config.php');
        $this->file          = vfsStream::newFile('test.config.php', 0777)->withContent($this->getConfigFileContent());

        $this->configFileURL2 = vfsStream::url('root/test2.config.php');
        $this->file2          = vfsStream::newFile('test2.config.php', 0777)->withContent('');

        $this->root = new vfsStreamDirectory('root');
        $this->root->addChild($this->file);
        $this->root->addChild($this->file2);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function getConfigFileContent()
    {
        return <<<EOF
<?php
// Configuration File generated by Koch Framework.
return array(
  "oldKey" => "value"
);
EOF;
    }

    public function getConfigArray()
    {
        return [
            'oldKey' => 'value',
        ];
    }

    /**
     * @covers Koch\Config\Adapter\PHP::read
     * @covers Koch\Config\AbstractConfig::toArray
     */
    public function testToArray()
    {
        $array = $this->object->read($this->configFileURL);
        $this->assertEquals($this->getConfigArray(), $this->object->toArray());

       // unset, returns the array one last time
       $this->assertEquals($array, $this->object->toArray(true));
        $this->assertEquals([], $this->object->toArray());
    }

    /**
     * @covers Koch\Config\AbstractConfig::merge
     */
    public function testMerge()
    {
        // read old config
        $this->object->read($this->configFileURL);

        // merge new values
        $newConfig = ['newKey' => 'newKeyValue'];
        $this->object->merge($newConfig);

        $this->assertArrayHasKey('oldKey', $this->object->toArray());
        $this->assertArrayHasKey('newKey', $this->object->toArray());
    }

    /**
     * @covers Koch\Config\AbstractConfig::getConfigValue
     */
    public function testGetConfigValue()
    {
        // read old config
        $this->object->read($this->configFileURL);

        $this->assertEquals('value', $this->object->getConfigValue('oldKey'));

        // not existing key, returns null
        $this->assertEquals(null, $this->object->getConfigValue('notExistingKey'));

        $this->assertEquals('defaultValue', $this->object->getConfigValue('notExistingKey', 'defaultValue'));

        $this->assertEquals(
            'defaultValueTwo',
            $this->object->getConfigValue('notExistingKey', null, 'defaultValueTwo')
        );

        $this->assertEquals(
            'defaultValue',
            $this->object->getConfigValue('notExistingKey', 'defaultValue', 'defaultValueTwo')
        );
    }

    /**
     * @covers Koch\Config\AbstractConfig::__get
     */
    public function test__get()
    {
        // read old config
        $this->object->read($this->configFileURL);
        $this->assertEquals('value', $this->object->oldKey);

        $this->assertNull($this->object->notExistingKey);
    }

    /**
     * @covers Koch\Config\AbstractConfig::__set
     * @covers Koch\Config\AbstractConfig::__isset
     * @covers Koch\Config\AbstractConfig::__unset
     */
    public function test__set()
    {
        // __set
        $this->object->newKey = 'someValue';
        $this->assertEquals('someValue', $this->object->newKey);

        // __isset
        $this->assertTrue(isset($this->object->newKey));

        // __unset
        unset($this->object->newKey);
        $this->assertFalse(isset($this->object->newKey));
    }

    /**
     * @covers Koch\Config\AbstractConfig::offsetExists
     */
    public function testOffsetExists()
    {
        $this->object->newKey = 'someValue';

        $this->assertFalse(empty($this->object['newKey']));
    }

    /**
     * @covers Koch\Config\AbstractConfig::offsetGet
     */
    public function testOffsetGet()
    {
        $this->object->newKey = 'someValue';

        $this->assertEquals('someValue', $this->object['newKey']);
    }

    /**
     * @covers Koch\Config\AbstractConfig::offsetSet
     */
    public function testOffsetSet()
    {
        $this->object['newKey'] = 'someValue';
        $this->assertEquals('someValue', $this->object['newKey']);
    }

    /**
     * @covers Koch\Config\AbstractConfig::offsetUnset
     *
     * @todo   Implement testOffsetUnset().
     */
    public function testOffsetUnset()
    {
        unset($this->object['newKey']);

        $this->assertFalse(isset($this->object->newKey));
    }
}
