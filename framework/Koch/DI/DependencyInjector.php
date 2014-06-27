<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\DI;

use Koch\DI\Engine\Context;
use Koch\DI\Engine\IncomingParameters;
use Koch\DI\Storage\ClassRepository;

/**
 * Koch Framework - Dependency Injector (Phemto by Markus Baker).
 *
 * Version: 0.1_alpha10 - SVN-Revision: 90
 *
 * @author Markus Baker
 * @license Public Domain
 * @link http://phemto.sourceforge.net/index.php
 */
class DependencyInjector
{
    public $repository;
    private $top;
    public $named_parameters = array();
    public $unnamed_parameters = array();

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
        $type = array_shift($values);
        $this->unnamed_parameters += $values;
        $this->repository = new ClassRepository();
        $object = $this->top->create($type);
        $this->named_parameters = array();

        return $object;
    }

    /**
     * @param string $type
     * @param string $candidates
     */
    public function pickFactory($type, $candidates)
    {
        throw new \Koch\DI\Exception\CannotDetermineImplementation($type);
    }

    /**
     * @param string $class
     */
    public function settersFor($class)
    {
        return array();
    }

    /**
     * @param string $type
     */
    public function wrappersFor($type)
    {
        return array();
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

        $value = array();
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
