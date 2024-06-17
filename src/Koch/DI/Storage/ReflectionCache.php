<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\DI\Storage;

class ReflectionCache
{
    private $implementations_of = [];
    private $interfaces_of      = [];
    private $reflections        = [];
    private $subclasses         = [];
    private $parents            = [];

    public function refresh()
    {
        $this->buildIndex(array_diff(get_declared_classes(), $this->indexed()));
        $this->subclasses = [];
    }

    public function implementationsOf($interface)
    {
        return isset($this->implementations_of[$interface]) ?
                $this->implementations_of[$interface] : [];
    }

    public function interfacesOf($class)
    {
        return isset($this->interfaces_of[$class]) ?
                $this->interfaces_of[$class] : [];
    }

    public function concreteSubgraphOf($class)
    {
        if (false === class_exists($class)) {
            return [];
        }

        if (false === isset($this->subclasses[$class])) {
            $this->subclasses[$class] = $this->isConcrete($class) ? [$class] : [];

            foreach ($this->indexed() as $candidate) {
                if (true === is_subclass_of($candidate, $class) && $this->isConcrete($candidate)) {
                    $this->subclasses[$class][] = $candidate;
                }
            }
        }

        return $this->subclasses[$class];
    }

    public function parentsOf($class)
    {
        if (false === isset($this->parents[$class])) {
            $this->parents[$class] = class_parents($class);
        }

        return $this->parents[$class];
    }

    public function reflection($class)
    {
        if (false === isset($this->reflections[$class])) {
            $this->reflections[$class] = new \ReflectionClass($class);
        }

        return $this->reflections[$class];
    }

    private function isConcrete($class)
    {
        return !$this->reflection($class)->isAbstract();
    }

    private function indexed()
    {
        return array_keys($this->interfaces_of);
    }

    private function buildIndex($classes)
    {
        foreach ($classes as $class) {
            $interfaces                  = array_values(class_implements($class));
            $this->interfaces_of[$class] = $interfaces;
            foreach ($interfaces as $interface) {
                $this->crossReference($interface, $class);
            }
        }
        # show class graph
        #var_export($this->implementations_of);
    }

    private function crossReference($interface, $class)
    {
        if (false === isset($this->implementations_of[$interface])) {
            $this->implementations_of[$interface] = [];
        }
        $this->implementations_of[$interface][] = $class;
        $this->implementations_of[$interface]   =
                array_values(array_keys(array_flip($this->implementations_of[$interface])));
    }
}
