<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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

namespace Koch\DI\Storage;

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
            [$class],
            self::$reflection->interfacesOf($class),
            self::$reflection->parentsOf($class)
        );

        return in_array($type, $supertypes, true);
    }

    public function getConstructorParameters($class)
    {
        $reflection = self::$reflection->reflection($class);

        $constructor = '';
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            return $constructor->getParameters();
        }

        return [];
    }

    public function getParameters($class, $method)
    {
        $reflection = self::$reflection->reflection($class);
        if (false === $reflection->hasMethod($method)) {
            throw new \Koch\DI\Exception\SetterDoesNotExist();
        }

        return $reflection->getMethod($method)->getParameters();
    }
}
