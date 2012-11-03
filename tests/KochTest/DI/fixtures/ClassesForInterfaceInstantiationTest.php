<?php
namespace KochTest\DI;

interface InterfaceWithOneImplementation { }
class OnlyImplementation implements InterfaceWithOneImplementation { }
interface InterfaceWithManyImplementations { }
class FirstImplementation implements InterfaceWithManyImplementations { }
class SecondImplementation implements InterfaceWithManyImplementations { }