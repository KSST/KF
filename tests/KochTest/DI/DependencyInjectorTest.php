<?php
namespace KochTest\DI;

use Koch\DI\DependencyInjector;
use Koch\DI\Lifecycle\Reused;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-11-03 at 18:13:32.
 */
class DependencyInjectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DependencyInjector
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->injector = new DependencyInjector;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->injector);
    }

    /**
     * @covers Koch\DI\DependencyInjector::willUse
     * @covers Koch\DI\DependencyInjector::create
     */
    public function testWillUse()
    {
        include_once __DIR__ . '/fixtures/ClassForSingletonInstantiationTest.php';

        // instantiate as singleton
        $this->injector->willUse(new Reused('KochTest\DI\CreateMeOnce'));
        $this->assertSame(
            $this->injector->create('KochTest\DI\CreateMeOnce'),
            $this->injector->create('KochTest\DI\CreateMeOnce')
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::register
     * @todo   Implement testRegister().
     */
    public function testRegister()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::forVariable
     * @todo   Implement testForVariable().
     */
    public function testForVariable()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::whenCreating
     * @covers Koch\DI\Engine\Context::wrapWith
     * @covers Koch\DI\DependencyInjector::create
     */
    public function testWhenCreating()
    {
        include_once __DIR__ . '/fixtures/ClassesForWhenCreatingTest.php';

        // 1) create concrete implemenation BareImplemenation via interface Bare
        // and 2) do a constructor injection of Bare into WrapperForBare
        $this->injector->whenCreating('KochTest\DI\Bare')->wrapWith('KochTest\DI\WrapperForBare');

        $this->assertEquals(
            $this->injector->create('KochTest\DI\Bare'),
            new WrapperForBare(new BareImplementation())
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::forType
     * @todo   Implement testForType().
     */
    public function testForType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::fill
     * @covers Koch\DI\DependencyInjector::with
     * @covers Koch\DI\DependencyInjector::create
     */
    public function testFill()
    {
       include_once __DIR__ . '/fixtures/ClassForParameterInjectionTest.php';

       // can fill missing parameters with explicit values
       $this->assertEquals(
                $this->injector->fill('a', 'b')->with(3, 5)->create('KochTest\DI\ClassWithParameters'),
                new ClassWithParameters(3, 5)
       );
    }

    /**
     * @covers Koch\DI\DependencyInjector::with
     * @covers Koch\DI\DependencyInjector::fill
     * @covers Koch\DI\DependencyInjector::create
     */
    public function testWith()
    {
        include_once __DIR__ . '/fixtures/ClassForParameterInjectionTest.php';

        // 1) can fill missing parameters with explicit values
        $this->assertEquals(
            $this->injector->with(3, 5)->create('KochTest\DI\ClassWithParameters'),
            new ClassWithParameters(3, 5)
        );

        // 2) can instantiate with named parameters
        $this->assertEquals(
            $this->injector->fill('a', 'b')->with(3, 5)->create('KochTest\DI\ClassWithParameters'),
            new ClassWithParameters(3, 5)
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::create
     * @covers Koch\DI\DependencyInjector::willUse
     * @covers Koch\DI\DependencyInjector::forVariable
     * @covers Koch\DI\DependencyInjector::forType
     * @covers Koch\DI\DependencyInjector::call
     */
    public function testCreate()
    {
        include_once __DIR__ . '/fixtures/ClassesForInjectionOfTypeHintsTest.php';

        // test injection of simple dependencies
        $this->assertEquals(
            $this->injector->create('KochTest\DI\HintedConstructor'),
            new HintedConstructor(new NeededForConstructor())
        );

        // test repeated type hint injection
        $this->injector->willUse('KochTest\DI\SecondImplementation');
        $this->assertEquals(
            $this->injector->create('KochTest\DI\RepeatedHintConstructor'),
            new RepeatedHintConstructor(
                new NeededForConstructor(),
                new NeededForConstructor()
            )
        );

        include_once __DIR__ . '/fixtures/ClassesForInjectionOfVariablesTest.php';

        // test variable injection
        $this->injector->forVariable('first')->willUse('KochTest\DI\NeededForFirst');
        $this->injector->forVariable('second')->willUse('KochTest\DI\NeededForSecond');
        $this->assertEquals(
                $this->injector->create('KochTest\DI\VariablesInConstructor'),
                new VariablesInConstructor(
                    new NeededForFirst(),
                    new NeededForSecond()
                )
        );

        include_once __DIR__ . '/fixtures/ClassForParameterInjectionTest.php';

        // test create with parameters
        // this is a short syntax form of the test #1 in testWith()
        $this->assertEquals(
            $this->injector->create('KochTest\DI\ClassWithParameters', 3, 5),
            new ClassWithParameters(3, 5)
        );

        include_once __DIR__ . '/fixtures/ClassForInjectionOfSpecificValuesTest.php';

        // test inject specific instance
        $this->injector->willUse(new Thing());
        $this->assertEquals(
            $this->injector->create('KochTest\DI\WrapThing'),
            new WrapThing(new Thing())
        );

        // test injecting specific instance for named variable
        $this->injector->forVariable('thing')->willUse(new Thing());
        $this->assertEquals(
            $this->injector->create('KochTest\DI\WrapAnything'),
            new WrapAnything(new Thing())
        );

        // test injecting non-object
        $this->injector->forVariable('thing')->willUse(100);
        $this->assertEquals(
            $this->injector->create('KochTest\DI\WrapAnything'),
            new WrapAnything(100)
        );

        // test injection string @todo
        /*$this->injector->forVariable('thing')->useString('100');
        $this->assertEquals(
            $this->injector->create('KochTest\DI\WrapAnything'), new WrapAnything('100')
        );*/

        include_once __DIR__ . '/fixtures/ClassesForAutoInstantiationTest.php';

        // test named class instantiated automatically
        $this->assertInstanceOf('KochTest\DI\LoneClass', $this->injector->create('KochTest\DI\LoneClass'));

        // test will use only subclass if parent class is abstract class
        $this->assertInstanceOf('KochTest\DI\ConcreteSubclass', $this->injector->create('KochTest\DI\AbstractClass'));

        // test can be configured to prefer a specific subclass
        $this->injector->willUse('KochTest\DI\SecondSubclass');
        $this->assertInstanceOf('KochTest\DI\SecondSubclass', $this->injector->create('KochTest\DI\ClassWithManySubclasses'));

        include_once __DIR__ . '/fixtures/ClassesForInterfaceInstantiationTest.php';

        $this->assertInstanceOf(
            'KochTest\DI\OnlyImplementation',
            $this->injector->create('KochTest\DI\InterfaceWithOneImplementation')
        );

        // can be configured to prefer specific implementation
        $this->injector->willUse('KochTest\DI\SecondImplementation');
        $this->assertInstanceOf(
            'KochTest\DI\SecondImplementation',
            $this->injector->create('KochTest\DI\InterfaceWithManyImplementations')
        );

        include_once __DIR__ . '/fixtures/ClassesForSetterInjectionTest.php';

        // test can call setters to complete initialisation
        $this->injector->forType('KochTest\DI\NeedsInitToCompleteConstruction')->call('init');
        $expected = new NeedsInitToCompleteConstruction();
        $expected->init(new NotWithoutMe()); // <-- setter injection
        $this->assertEquals(
            $this->injector->create('KochTest\DI\NeedsInitToCompleteConstruction'),
            $expected
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::create
     * @expectedException Koch\DI\Exception\CannotFindImplementation
     * @expectedExceptionMessage InterfaceWithManyImplementations
     */
    public function testCreate_creatingInterfaceWithManyImplementationsThrowsException()
    {
        include_once __DIR__ . '/fixtures/ClassesForInterfaceInstantiationTest.php';

        $this->injector->create('InterfaceWithManyImplementations');
    }

    /**
     * @covers Koch\DI\DependencyInjector::create
     * @expectedException Koch\DI\Exception\CannotFindImplementation
     * @expectedExceptionMessage NonExistingClass
     */
    public function testCreate_creatingNonExistingClassThrowsException()
    {
        $this->injector->create('NonExistingClass');
    }

    /**
     * @covers Koch\DI\DependencyInjector::instantiate
     * @todo   Implement testInstantiate().
     */
    public function testInstantiate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::pickFactory
     * @todo   Implement testPickFactory().
     */
    public function testPickFactory()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::settersFor
     * @todo   Implement testSettersFor().
     */
    public function testSettersFor()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::wrappersFor
     * @todo   Implement testWrappersFor().
     */
    public function testWrappersFor()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::useParameters
     * @todo   Implement testUseParameters().
     */
    public function testUseParameters()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::instantiateParameter
     * @todo   Implement testInstantiateParameter().
     */
    public function testInstantiateParameter()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::repository
     * @todo   Implement testRepository().
     */
    public function testRepository()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

}
