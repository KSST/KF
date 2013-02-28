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

namespace Koch\PHPUnit;

use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;

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
    protected $em;

    public function setUp()
    {
        parent::setUp();

        //if(!in_array('sqlite', \PDO::getAvailableDrivers())) {
        if(!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('This test requires the PHP extension "pdo_sqlite".');
        }

        // setup Annotation Registry
        AnnotationRegistry::registerFile(
            __DIR__.'/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
        );

        // setup Annotation Driver
        $driver = AnnotationDriver::create(__DIR__ . '/KochTest/Fixtures/Doctrine/Entity');

        $config = new Configuration();
        $config->setMetadataDriverImpl($driver);
        $config->setQueryCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/KochTest/Fixtures/Doctrine/Proxy');
        $config->setProxyNamespace('/KochTest/Fixtures/Doctrine/Entity');

        $connectionParams = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $this->em = \Doctrine\ORM\EntityManager::create($connectionParams, $config);
    }

    /**
     * Getter for the EntityManager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }
}
