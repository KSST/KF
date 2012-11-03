<?php
namespace KochTest\DI;

class WrapThing {
    function __construct(Thing $thing) { $this->thing = $thing; }
}

class WrapAnything {
    function __construct($thing) { $this->thing = $thing; }
}

class Thing { }
