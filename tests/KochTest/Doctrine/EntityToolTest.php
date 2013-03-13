<?php
namespace KochTest\Doctrine;

use Koch\Doctrine\EntityTool;
use Koch\PHPUnit\DoctrineTestCase;

class EntityToolTest extends DoctrineTestCase
{
    /**
     * @var CreateEntity
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();

        $this->object = new EntityTool($this->em);

        // force Doctrine annotations to be loaded
        class_exists('Doctrine\ORM\Mapping\Driver\AnnotationDriver');
    }

    /**
     * @covers Koch\Doctrine\EntityTool::createEntity
     */
    public function testCreateEntity()
    {
        $data = array(
            'name' => 'Ford',
            'price' => 15000
        );

        $product = $this->object->createEntity(
            new \KochTest\Fixtures\Doctrine\Entity\Product(),
            $data
        );

        $this->assertEquals($data['name'], $product->getName());
        $this->assertEquals($data['price'], $product->getPrice());
    }

    /**
     * expectedException \InvalidPropertyException
     */
    public function testCannotAddInvalidProperty()
    {
        $data = array(
            'name' => 'Ford',
            'category' => 'Cars'
        );

        $product = $this->object->createEntity(
            new \KochTest\Fixtures\Doctrine\Entity\Product(),
            $data
        );
    }
}
