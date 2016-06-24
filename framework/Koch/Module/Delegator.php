<?php

namespace Koch\Module;

use ReflectionClass;

class Delegator
{
    /**
     * @var object delegated object
     */
    protected $delegate;

    public function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    public function getDelegate()
    {
        return $this->delegate;
    }

    public function __call($name, $args)
    {
        $r = new ReflectionClass($this);
        if ($r->hasMethod($name)) {
            $method = $r->getMethod($name);
            if ($method->isPublic() && false === $method->isAbstract()) {
                call_user_func_array([$this, $name], $args);
            }
        }

        return call_user_func_array([$this->delegate, $name], $args);
    }
}
