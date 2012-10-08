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

namespace Koch\DI\Storage;

use Koch\DI\Storage\ReflectionCache;

class ClassRepository
{
    private static $reflection = false;

    public function __construct()
    {
        if (false === self::$reflection) {
            self::$reflection = new ReflectionCache();
        }
        self::$reflection->refresh();
    }

    public function candidatesFor($interface)
    {
        return array_merge(
           self::$reflection->concreteSubgraphOf($interface),
           self::$reflection->implementationsOf($interface)
        );
    }

    public function isSupertype($class, $type)
    {
        $supertypes = array_merge(
            array($class),
            self::$reflection->interfacesOf($class),
            self::$reflection->parentsOf($class)
        );

        return in_array($type, $supertypes);
    }

    public function getConstructorParameters($class)
    {
        $reflection = self::$reflection->reflection($class);

        $constructor = '';
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            return $constructor->getParameters();
        }

        return array();
    }

    public function getParameters($class, $method)
    {
        $reflection = self::$reflection->reflection($class);
        if (false === $reflection->hasMethod($method)) {
            throw new SetterDoesNotExist();
        }

        return $reflection->getMethod($method)->getParameters();
    }
}
