<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\DI;

use Koch\DI\Engine\Context;
use Koch\DI\Storage\ClassRepository;
use Koch\DI\Exception\CannotDetermineImplementation;
use Koch\DI\Exception\MissingDependency;

/**
 * Dependency Injector.
 *
 * This DI is based on Phemto by Markus Baker.
 *
 * Version: 0.1_alpha10 - SVN-Revision: 90
 * @link http://phemto.sourceforge.net/index.php
 *
 */
class DependencyInjector
{
    private $top;
    private $named_parameters = array();
    private $unnamed_parameters = array();

    public function __construct()
    {
        $this->top = new Context($this);
    }

    public function willUse($preference)
    {
        $this->top->willUse($preference);
    }

    public function register($preference)
    {
        $this->top->willUse($preference);
    }

    public function forVariable($name)
    {
        return $this->top->forVariable($name);
    }

    public function whenCreating($type)
    {
        return $this->top->whenCreating($type);
    }

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
        $this->unnamed_parameters = array_merge($this->unnamed_parameters, $values);

        return $this;
    }

    public function create()
    {
        $values = func_get_args();
        $type = array_shift($values);
        $this->unnamed_parameters = array_merge($this->unnamed_parameters, $values);
        $this->repository = new ClassRepository();
        $object = $this->top->create($type);
        $this->named_parameters = array();

        return $object;
    }

    public function instantiate()
    {
        $values = func_get_args();
        $type = array_shift($values);
        $this->unnamed_parameters = array_merge($this->unnamed_parameters, $values);
        $this->repository = new ClassRepository();
        $object = $this->top->create($type);
        $this->named_parameters = array();

        return $object;
    }

    public function pickFactory($type, $candidates)
    {
        throw new CannotDetermineImplementation($type);
    }

    public function settersFor($class)
    {
        return array();
    }

    public function wrappersFor($type)
    {
        return array();
    }

    public function useParameters($parameters)
    {
        $this->named_parameters = array_merge($this->named_parameters, $parameters);
    }

    public function instantiateParameter($parameter, $nesting)
    {
        $name = $parameter->getName();
        
        if (true === isset($this->named_parameters[$name])) {
            return $this->named_parameters[$name];
        }

        $value = array();
        $value = array_shift($this->unnamed_parameters);
        if ($value) {
            return $value;
        }

        throw new MissingDependency($name);
    }

    public function repository()
    {
        return $this->repository;
    }
}
