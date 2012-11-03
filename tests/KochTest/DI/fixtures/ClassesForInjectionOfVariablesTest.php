<?php
namespace KochTest\DI;

class NeededForFirst { }
class NeededForSecond { }

class VariablesInConstructor {
    function __construct($first, $second) {
        $this->args = array($first, $second);
    }
}
