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

namespace Koch\DI;

use Koch\DI\Engine\Context;
use Koch\DI\Engine\IncomingParameters;
use Koch\DI\Storage\ClassRepository;

/**
 * Dependency Injector.
 *
 * This DI is based on Phemto by Markus Baker.
 *
 * Version: 0.1_alpha10 - SVN-Revision: 90
 * @link http://phemto.sourceforge.net/index.php
 */
class DependencyInjector
{
    public $repository;
    private $top;
    public $named_parameters   = [];
    public $unnamed_parameters = [];

    public function __construct()
    {
        $this->top = new Context($this);
    }

    public function register($preference)
    {
        $this->top->willUse($preference);
    }

    /**
     * @param string $name
     */
    public function forVariable($name)
    {
        return $this->top->forVariable($name);
    }

    /**
     * @param string $type
     */
    public function whenCreating($type)
    {
        return $this->top->whenCreating($type);
    }

    /**
     * @param string $type
     */
    public function forType($type)
    {
        return $this->top->forType($type);
    }

    public function fill()
    {
        $names = func_get_args();

        return new IncomingParameters($names, $this);
    }

    public function with()
    {
        $values = func_get_args();
        $this->unnamed_parameters += $values;

        return $this;
    }

    public function instantiate()
    {
        $values = func_get_args();
        $type   = array_shift($values);
        $this->unnamed_parameters += $values;
        $this->repository       = new ClassRepository();
        $object                 = $this->top->create($type);
        $this->named_parameters = [];

        return $object;
    }

    /**
     * @param string $type
     * @param string $candidates
     */
    public function pickFactory($type, $candidates): never
    {
        throw new \Koch\DI\Exception\CannotDetermineImplementation($type);
    }

    /**
     * @param string $class
     */
    public function settersFor($class)
    {
        return [];
    }

    /**
     * @param string $type
     */
    public function wrappersFor($type)
    {
        return [];
    }

    public function useParameters($parameters)
    {
        $this->named_parameters += $parameters;
    }

    /**
     * @param string $nesting
     */
    public function instantiateParameter($parameter, $nesting)
    {
        $name = $parameter->getName();

        if (true === isset($this->named_parameters[$name])) {
            return $this->named_parameters[$name];
        }

        $value = [];
        $value = array_shift($this->unnamed_parameters);
        if ($value) {
            return $value;
        }

        throw new \Koch\DI\Exception\MissingDependency($name);
    }

    public function repository()
    {
        return $this->repository;
    }
}
