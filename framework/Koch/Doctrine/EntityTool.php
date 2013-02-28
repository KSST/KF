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

namespace Koch\Doctrine;

use Doctrine\ORM\EntityManager;

/**
 * Koch Framework - Class for manipulating entities.
 */
class EntityTool
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Creates an entity with the given data.
     *
     * @param  string|object $entity
     * @param  array         $data
     * @return object
     */
    public function createEntity($entity, array $data)
    {
        $class = is_object($entity) ? get_class($entity) : $entity;
        $metadata = $this->em->getClassMetadata($class);
        $entity = $metadata->newInstance();

        foreach ($data as $property => $value) {
            if (!$metadata->reflClass->hasProperty($property)) {
                throw new \InvalidArgumentException(
                    sprintf('Property "%s" does not exist on class "%s".', $property, $class)
                );
            }

            $metadata->setFieldValue($entity, $property, $value);
        }

        return $entity;
    }
}
