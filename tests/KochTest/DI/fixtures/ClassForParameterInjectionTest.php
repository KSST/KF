<?php

namespace KochTest\DI;

class ClassWithParameters
{

    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

}
