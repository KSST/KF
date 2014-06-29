<?php

namespace KochTest\DI;

class NeededForFirst
{

}

class NeededForSecond
{

}

class VariablesInConstructor
{

    /**
     * @param NeededForFirst  $first
     * @param NeededForSecond $second
     */
    public function __construct($first, $second)
    {
        $this->args = array($first, $second);
    }

}
