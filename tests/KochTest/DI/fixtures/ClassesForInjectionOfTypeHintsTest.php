<?php
namespace KochTest\DI;

interface Hinted { }

class NeededForConstructor { }

class HintedConstructor implements Hinted {
    function __construct(NeededForConstructor $one) {
        $this->one = $one;
    }
}

class HintedConstructorWithDependencyChoice implements Hinted {
    function __construct(InterfaceWithManyImplementations $alternate) {
        $this->alternate = $alternate;
    }
}

class RepeatedHintConstructor {
    function __construct(NeededForConstructor $first, NeededForConstructor $second) {
        $this->args = array($first, $second);
    }
}
