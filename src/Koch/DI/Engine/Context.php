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

namespace Koch\DI\Engine;

use Koch\DI\AbstractLifecycle;
use Koch\DI\Exception\CannotFindImplementation;
use Koch\DI\Lifecycle\Factory;
use Koch\DI\Lifecycle\Value;

class Context
{
    /*private $repository;*/
    private $registry  = [];
    private $variables = [];
    private $contexts  = [];
    private $types     = [];
    private $wrappers  = [];

    public function __construct(private $parent)
    {
    }

    public function willUse($preference)
    {
        if ($preference instanceof AbstractLifecycle) {
            $lifecycle = $preference;
        } elseif (true === is_object($preference)) {
            $lifecycle = new Value($preference);
        } else {
            $lifecycle = new Factory($preference);
        }
        array_unshift($this->registry, $lifecycle);
    }

    /**
     * @param string $name
     */
    public function forVariable($name)
    {
        return $this->variables[$name] = new Variable($this);
    }

    /**
     * @param string $type
     */
    public function whenCreating($type)
    {
        if (false === isset($this->contexts[$type])) {
            $this->contexts[$type] = new self($this);
        }

        return $this->contexts[$type];
    }

    /**
     * @param string $type
     */
    public function forType($type)
    {
        if (false === isset($this->types[$type])) {
            $this->types[$type] = new Type();
        }

        return $this->types[$type];
    }

    public function wrapWith($type)
    {
        array_push($this->wrappers, $type);
    }

    public function create($type, $nesting = [])
    {
        $lifecycle = $this->pickFactory($type, $this->repository()->candidatesFor($type));
        $context   = $this->determineContext($lifecycle->class);
        $wrapper   = $context->hasWrapper($type, $nesting);

        if ($wrapper) {
            return $this->create($wrapper, $this->cons($wrapper, $nesting));
        }

        $instance = $lifecycle->instantiate(
            $context->createDependencies(
                $this->repository()->getConstructorParameters($lifecycle->class),
                $this->cons($lifecycle->class, $nesting)
            )
        );

        $this->invokeSetters($context, $nesting, $lifecycle->class, $instance);

        return $instance;
    }

    public function pickFactory($type, $candidates)
    {
        if (count($candidates) === 0) {
            throw new CannotFindImplementation($type);
        }

        $preference = $this->preferFrom($candidates);
        if ($preference) {
            return $preference;
        }

        if (count($candidates) === 1) {
            return new Factory($candidates[0]);
        }

        return $this->parent->pickFactory($type, $candidates);
    }

    public function hasWrapper($type, $already_applied)
    {
        $wrappers = $this->wrappersFor($type);
        foreach ($wrappers as $wrapper) {
            if (false === in_array($wrapper, $already_applied, true)) {
                return $wrapper;
            }
        }

        return false;
    }

    private function invokeSetters($context, $nesting, $class, $instance)
    {
        foreach ($context->settersFor($class) as $setter) {
            $context->invoke(
                $instance,
                $setter,
                $context->createDependencies(
                    $this->repository()->getParameters($class, $setter),
                    $this->cons($class, $nesting)
                )
            );
        }
    }

    private function settersFor($class)
    {
        $setters = isset($this->types[$class]) ? $this->types[$class]->setters : [];

        return array_values(array_keys(array_flip(array_merge($setters, $this->parent->settersFor($class)))));
    }

    public function wrappersFor($type)
    {
        return array_values(array_merge($this->wrappers, $this->parent->wrappersFor($type)));
    }

    public function createDependencies($parameters, $nesting)
    {
        $values = [];
        foreach ($parameters as $parameter) {
            try {
                $values[] = $this->instantiateParameter($parameter, $nesting);
            } catch (\Exception $e) {
                if ($parameter->isOptional()) {
                    break;
                }
                throw $e;
            }
        }

        return $values;
    }

    private function instantiateParameter($parameter, $nesting)
    {
        $hint = $parameter->getClass();
        if ($hint) {
            return $this->create($hint->getName(), $nesting);
        }

        if (true === isset($this->variables[$parameter->getName()])) {
            if ($this->variables[$parameter->getName()]->preference instanceof AbstractLifecycle) {
                return $this->variables[$parameter->getName()]->preference->instantiate([]);
            }

            if (false === is_string($this->variables[$parameter->getName()]->preference)) {
                return $this->variables[$parameter->getName()]->preference;
            }

            return $this->create($this->variables[$parameter->getName()]->preference, $nesting);
        }

        return $this->parent->instantiateParameter($parameter, $nesting);
    }

    private function determineContext($class)
    {
        foreach ($this->contexts as $type => $context) {
            if (true === $this->repository()->isSupertype($class, $type)) {
                return $context;
            }
        }

        return $this;
    }

    private function invoke($instance, $method, $arguments)
    {
        call_user_func_array([$instance, $method], $arguments);
    }

    private function preferFrom($candidates)
    {
        foreach ($this->registry as $preference) {
            if (true === $preference->isOneOf($candidates)) {
                return $preference;
            }
        }

        return false;
    }

    private function cons($head, $tail)
    {
        array_unshift($tail, $head);

        return $tail;
    }

    public function repository()
    {
        return $this->parent->repository();
    }
}
