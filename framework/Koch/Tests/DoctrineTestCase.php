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

namespace Koch\Tests;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;

/**
 * Koch Framework - Base class for all unit-tests working with the Doctrine2 ORM.
 *
 * The test helper "DoctrineTestCase" is the base class for all unit-tests
 * working with the Doctrine2 ORM. It provides the Doctrine2 EntityManager.
 * Use it, by replacing "extends \PHPUnit_Framework_TestCase" in your test class,
 * with "extends \DoctrineTestCase". Then $this->em becomes available.
 * This class extends our TestCase class, which provides
 * some basic additional functionality (reflection, access manipulation).
 */
class DoctrineTestCase extends TestCase
{
    /* @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    public function setUp()
    {
        parent::setUp();

        //if (!in_array('sqlite', \PDO::getAvailableDrivers())) {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('This test requires the PHP extension "pdo_sqlite".');
        }

        // this is the same as AnnotationRegistry::registerFile()
        require_once VENDOR_PATH . 'doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php';

        // setup Annotation Driver
        $driver = new AnnotationDriver(new AnnotationReader(), array(
            __DIR__ . '/../../../tests/KochTest/Fixtures/Doctrine/Entity',
        ));

        $config = new Configuration();
        $config->setMetadataDriverImpl($driver);
        $config->addEntityNamespace('KochTest', 'KochTest\Fixtures\Doctrine\Entity');

        $config->setQueryCacheImpl(new ArrayCache());
        $config->setProxyDir(__DIR__ . '/../../../tests/KochTest/Fixtures/Doctrine/Entity');
        $config->setProxyNamespace('/KochTest/Fixtures/Doctrine/Entity');

        $connectionParams = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $em = EntityManager::create($connectionParams, $config);

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $this->entityManager = $em;
    }

    /**
     * Getter for the EntityManager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
