<?php

namespace KochTest\Config;

use Koch\Config\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Factory();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    /**
     * @covers Koch\Config\Factory::determineConfigurationHandlerTypeBy
     * @expectedException \Koch\Exception\Exception
     * @expectedExceptionMessage Unknown file extension.
     */
    public function testDetermineConfigurationHandlerTypeBy_throwsExceptionOnInvalidFileExtension()
    {
        $configfile = 'file.with.unknown.extension';
        $this->object->determineConfigurationHandlerTypeBy($configfile);
    }

    /**
     * @covers Koch\Config\Factory::determineConfigurationHandlerTypeBy
     */
    public function testDetermineConfigurationHandlerTypeBy()
    {
        $configfile = 'file.config.php';
        $er         = $this->object->determineConfigurationHandlerTypeBy($configfile);
        $this->assertEquals($er, 'PHP');

        $configfile = 'file.config.ini';
        $er         = $this->object->determineConfigurationHandlerTypeBy($configfile);
        $this->assertEquals($er, 'INI');

        $configfile = 'file.config.xml';
        $er         = $this->object->determineConfigurationHandlerTypeBy($configfile);
        $this->assertEquals($er, 'XML');

        $configfile = 'file.config.yaml';
        $er         = $this->object->determineConfigurationHandlerTypeBy($configfile);
        $this->assertEquals($er, 'YAML');
    }

    /**
     * @covers Koch\Config\Factory::getConfiguration
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage XML File file.config.xml not existing or not readable.
     */
    public function testGetConfiguration()
    {
        $configfile = 'file.config.xml';
        $er         = $this->object->getConfiguration($configfile);
        $this->assertEquals($er, '');
    }

    /**
     * @covers Koch\Config\Factory::getHandler
     */
    public function testGetHandler()
    {
        $configfile = 'file.config.xml';
        $handler    = $this->object->getHandler($configfile);
        $this->assertInstanceOf('Koch\Config\Adapter\XML', $handler);
    }

    /**
     * @covers Koch\Config\Factory::getAdapter
     */
    public function testGetAdapter()
    {
        $adapter = 'xml';
        $handler = $this->object->getAdapter($adapter);
        $this->assertInstanceOf('Koch\Config\Adapter\XML', $handler);
    }
}
