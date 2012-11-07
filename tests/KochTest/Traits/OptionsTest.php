<?php
namespace KochTest\Traits;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $PHP_FEATURE = 'Traits';
        $REQUIRED_PHP_VERSION = '5.4.0';
        if (version_compare(PHP_VERSION, $REQUIRED_PHP_VERSION, '<=') === true) {
            $this->markTestSkipped(
                $PHP_FEATURE . ' will only work with PHP ' . $REQUIRED_PHP_VERSION . '.'
                . ' Your PHP Version is ' . PHP_VERSION . '.'
            );
        }

        $this->object = new AClassUsingTraits;
    }

    public static function getArrayData()
    {
        return array(
            'firstname' => 'Low',
            'lastname' => 'Bob'
        );
    }

    /**
     * @covers Koch\Traits\Options::setOption
     * @covers Koch\Traits\Options::getOption
     */
    public function testSetOption()
    {
        $this->object->setOption('firstname', 'Jens');
        $this->assertEquals('Jens', $this->object->getOption('firstname'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Option firstname does not exist.
     */
    public function testGetOptionThrowsException()
    {
        $this->object->getOption('firstname');
    }

    /**
     * @covers Koch\Traits\Options::setOptions
     * @covers Koch\Traits\Options::getOptions
     */
    public function testSetOptions()
    {
        $options = self::getArrayData();
        $this->object->setOptions($options);

        $this->assertEquals($options, $this->object->getOptions());
    }
}
