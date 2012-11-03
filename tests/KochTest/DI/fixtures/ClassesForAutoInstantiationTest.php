<?php
namespace KochTest\DI;

class LoneClass { }
class ClassWithManySubclasses { }
class FirstSubclass extends ClassWithManySubclasses { }
class SecondSubclass extends ClassWithManySubclasses { }
abstract class AbstractClass { }
class ConcreteSubclass extends AbstractClass { }