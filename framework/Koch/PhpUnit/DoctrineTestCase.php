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

namespace Koch\PhpUnit;

use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * Base class for all unit-tests working with the Doctrine2 ORM.
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

        if (!extension_loaded('pdo_sqlite') or !extension_loaded('sqlite')) {
            $this->markTestSkipped('This test requires the php extension "pdo_sqlite" or "sqlite".');
        }

        $driver = AnnotationDriver::create(__DIR__ . '/KochTest/Fixtures/Doctrine/Entity');

        $config = new Configuration();
        $config->setMetadataDriverImpl($driver);
        $config->setQueryCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/KochTest/Fixtures/Doctrine/Proxy');
        $config->setProxyNamespace('/KochTest/Fixtures/Doctrine/Entity');

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $this->em = \Doctrine\ORM\EntityManager::create($conn, $config);
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
