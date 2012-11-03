<?php
namespace KochTest\DI;

class ClassWithParameters
{
    function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }
}
