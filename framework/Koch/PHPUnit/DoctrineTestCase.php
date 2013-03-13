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

namespace Koch\PHPUnit;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\AnnotationRegistry;

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

        //if(!in_array('sqlite', \PDO::getAvailableDrivers())) {
        if(!extension_loaded('pdo_sqlite')) {
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

        $this->em = $em;
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
