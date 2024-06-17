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

namespace Koch\Doctrine;

use Doctrine\ORM\EntityManager;

/**
 * Class for manipulating and creating entities.
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
     * @param string|object $entity
     *
     * @return object
     */
    public function createEntity($entity, array $data)
    {
        $class    = is_object($entity) ? $entity::class : $entity;
        $metadata = $this->em->getClassMetadata($class);
        $entity   = $metadata->newInstance();

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
