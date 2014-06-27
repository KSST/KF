<?php

namespace KochTest\DI;

class ClassWithParameters
{

    /**
     * @param integer $a
     * @param integer $b
     */
    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

}
