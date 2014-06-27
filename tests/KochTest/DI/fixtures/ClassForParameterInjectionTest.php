<?php

namespace KochTest\DI;

class ClassWithParameters
{

    /**
     * @param int $a
     * @param int $b
     */
    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

}
