<?php
namespace KochTest\DI;

use Koch\DI\DependencyInjector;
use Koch\DI\Lifecycle\Reused;

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
     * @covers Koch\DI\DependencyInjector::register
     * @covers Koch\DI\DependencyInjector::instantiate
     * @covers Koch\DI\DependencyInjector::register
     */
    public function testRegister()
    {
        include_once __DIR__ . '/fixtures/ClassForSingletonInstantiationTest.php';

        // instantiate as singleton
        $this->injector->register(new Reused('KochTest\DI\CreateMeOnce'));
        $this->assertSame(
            $this->injector->instantiate('KochTest\DI\CreateMeOnce'),
            $this->injector->instantiate('KochTest\DI\CreateMeOnce')
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::forVariable
     * @covers Koch\DI\DependencyInjector::register
     * @covers Koch\DI\DependencyInjector::instantiate
     * @covers Koch\DI\Engine\Variable::willUse
     */
    public function testForVariable()
    {
        include_once __DIR__ . '/fixtures/ClassesForInjectionOfVariablesTest.php';

        // test variable injection
        $this->injector->forVariable('first')->willUse('KochTest\DI\NeededForFirst');
        $this->injector->forVariable('second')->willUse('KochTest\DI\NeededForSecond');
        $this->assertEquals(
                $this->injector->instantiate('KochTest\DI\VariablesInConstructor'),
                new VariablesInConstructor(
                    new NeededForFirst(),
                    new NeededForSecond()
                )
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::whenCreating
     * @covers Koch\DI\Engine\Context::wrapWith
     * @covers Koch\DI\DependencyInjector::instantiate
     */
    public function testWhenCreating()
    {
        include_once __DIR__ . '/fixtures/ClassesForWhenCreatingTest.php';

        // 1) create concrete implemenation BareImplemenation via interface Bare
        // and 2) do a constructor injection of Bare into WrapperForBare
        $this->injector->whenCreating('KochTest\DI\Bare')->wrapWith('KochTest\DI\WrapperForBare');

        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\Bare'),
            new WrapperForBare(new BareImplementation())
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::forType
     * @covers Koch\DI\Engine\Type::call
     * @covers Koch\DI\DependencyInjector::instantiate
     */
    public function testForType()
    {
        include_once __DIR__ . '/fixtures/ClassesForSetterInjectionTest.php';

        // test can call setters to complete initialisation
        $this->injector->forType('KochTest\DI\NeedsInitToCompleteConstruction')->call('init');
        $expected = new NeedsInitToCompleteConstruction();
        $expected->init(new NotWithoutMe()); // <-- setter injection
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\NeedsInitToCompleteConstruction'),
            $expected
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::fill
     * @covers Koch\DI\DependencyInjector::with
     * @covers Koch\DI\DependencyInjector::instantiate
     */
    public function testFill()
    {
       include_once __DIR__ . '/fixtures/ClassForParameterInjectionTest.php';

       // can fill missing parameters with explicit values
       $this->assertEquals(
                $this->injector->fill('a', 'b')->with(3, 5)->instantiate('KochTest\DI\ClassWithParameters'),
                new ClassWithParameters(3, 5)
       );
    }

    /**
     * @covers Koch\DI\DependencyInjector::with
     * @covers Koch\DI\DependencyInjector::fill
     * @covers Koch\DI\DependencyInjector::instantiate
     */
    public function testWith()
    {
        include_once __DIR__ . '/fixtures/ClassForParameterInjectionTest.php';

        // 1) can fill missing parameters with explicit values
        $this->assertEquals(
            $this->injector->with(3, 5)->instantiate('KochTest\DI\ClassWithParameters'),
            new ClassWithParameters(3, 5)
        );

        // 2) can instantiate with named parameters
        $this->assertEquals(
            $this->injector->fill('a', 'b')->with(3, 5)->instantiate('KochTest\DI\ClassWithParameters'),
            new ClassWithParameters(3, 5)
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::instantiate
     * @covers Koch\DI\DependencyInjector::instantiate
     * @covers Koch\DI\DependencyInjector::register
     * @covers Koch\DI\DependencyInjector::forVariable
     * @covers Koch\DI\DependencyInjector::forType
     * @covers Koch\DI\Engine\Type::call
     * @covers Koch\DI\Engine\Variable::willUse
     */
    public function testCreate()
    {
        include_once __DIR__ . '/fixtures/ClassesForInjectionOfTypeHintsTest.php';

        // test injection of simple dependencies
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\HintedConstructor'),
            new HintedConstructor(new NeededForConstructor())
        );

        // test repeated type hint injection
        $this->injector->register('KochTest\DI\SecondImplementation');
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\RepeatedHintConstructor'),
            new RepeatedHintConstructor(
                new NeededForConstructor(),
                new NeededForConstructor()
            )
        );

        include_once __DIR__ . '/fixtures/ClassForParameterInjectionTest.php';

        // test create with parameters
        // this is a short syntax form of the test #1 in testWith()
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\ClassWithParameters', 3, 5),
            new ClassWithParameters(3, 5)
        );

        include_once __DIR__ . '/fixtures/ClassForInjectionOfSpecificValuesTest.php';

        // test inject specific instance
        $this->injector->register(new Thing());
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\WrapThing'),
            new WrapThing(new Thing())
        );

        // test injecting specific instance for named variable
        $this->injector->forVariable('thing')->willUse(new Thing());
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\WrapAnything'),
            new WrapAnything(new Thing())
        );

        // test injecting non-object
        $this->injector->forVariable('thing')->willUse(100);
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\WrapAnything'),
            new WrapAnything(100)
        );

        // test injection string @todo
        /*$this->injector->forVariable('thing')->useString('100');
        $this->assertEquals(
            $this->injector->instantiate('KochTest\DI\WrapAnything'), new WrapAnything('100')
        );*/

        include_once __DIR__ . '/fixtures/ClassesForAutoInstantiationTest.php';

        // test named class instantiated automatically
        $this->assertInstanceOf('KochTest\DI\LoneClass', $this->injector->instantiate('KochTest\DI\LoneClass'));

        // test will use only subclass if parent class is abstract class
        $this->assertInstanceOf('KochTest\DI\ConcreteSubclass', $this->injector->instantiate('KochTest\DI\AbstractClass'));

        // test can be configured to prefer a specific subclass
        $this->injector->register('KochTest\DI\SecondSubclass');
        $this->assertInstanceOf('KochTest\DI\SecondSubclass', $this->injector->instantiate('KochTest\DI\ClassWithManySubclasses'));

        include_once __DIR__ . '/fixtures/ClassesForInterfaceInstantiationTest.php';

        $this->assertInstanceOf(
            'KochTest\DI\OnlyImplementation',
            $this->injector->instantiate('KochTest\DI\InterfaceWithOneImplementation')
        );

        // can be configured to prefer specific implementation
        $this->injector->register('KochTest\DI\SecondImplementation');
        $this->assertInstanceOf(
            'KochTest\DI\SecondImplementation',
            $this->injector->instantiate('KochTest\DI\InterfaceWithManyImplementations')
        );
    }

    /**
     * @covers Koch\DI\DependencyInjector::instantiate
     * @expectedException Koch\DI\Exception\CannotFindImplementation
     * @expectedExceptionMessage InterfaceWithManyImplementations
     */
    public function testCreate_creatingInterfaceWithManyImplementationsThrowsException()
    {
        include_once __DIR__ . '/fixtures/ClassesForInterfaceInstantiationTest.php';

        $this->injector->instantiate('InterfaceWithManyImplementations');
    }

    /**
     * @covers Koch\DI\DependencyInjector::instantiate
     * @expectedException Koch\DI\Exception\CannotFindImplementation
     * @expectedExceptionMessage NonExistingClass
     */
    public function testCreate_creatingNonExistingClassThrowsException()
    {
        $this->injector->instantiate('NonExistingClass');
    }

    /**
     * @covers Koch\DI\DependencyInjector::pickFactory
     * @expectedException \Koch\DI\Exception\CannotDetermineImplementation
     */
    public function testPickFactory()
    {
        $this->object->pickFactory($type, $candidates);
    }

    /**
     * @covers Koch\DI\DependencyInjector::settersFor
     */
    public function testSettersFor()
    {
        $this->assertEquals(array(), $this->object->settersFor());
    }

    /**
     * @covers Koch\DI\DependencyInjector::wrappersFor
     */
    public function testWrappersFor()
    {
        $this->assertEquals(array(), $this->object->wrappersFor());
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
     */
    public function testRepository()
    {
        $this->assertEquals($this->repository, $this->repository());
    }

}
