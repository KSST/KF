<?php

namespace KochTest\DI;

class WrapThing
{
    public function __construct(Thing $thing)
    {
        $this->thing = $thing;
    }
}

class WrapAnything
{
    public function __construct($thing)
    {
        $this->thing = $thing;
    }
}

class Thing
{
}
