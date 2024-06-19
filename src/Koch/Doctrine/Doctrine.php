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

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\Common\EventManager;
use Gedmo\DoctrineExtensions;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

/**
 * Koch Framework - Class for the Initialization of Doctrine 2.
 */
class Doctrine
{
    /**
     * A DBAL Logger Object.
     *
     * @var object \Doctrine\DBAL\Logging\DebugStack
     */
    private static $sqlLoggerStack = '';

    /**
     * Doctrine2 Entity Manager.
     *
     * @var object \Doctrine\ORM\EntityManager
     */
    private static $em;

    /**
     * Ensure that Database Connection Informations are present
     * in configuration. If not, point back to the installation.
     */
    public static function optionsContainDSN($config)
    {
        // check if database settings are available
        if (
            empty($config['database']['driver'])
            || empty($config['database']['user'])
            || empty($config['database']['host'])
            || empty($config['database']['dbname'])
        ) {
            $msg1 = _('The database connection configuration is missing.');
            $msg2 = _('Please use <a href=%s>Installation</a> to perform a proper installation.');

            $uri = sprintf('http://%s%s', $_SERVER['SERVER_NAME'], '/installation/index.php');

            $msg = $msg1 . NL . sprintf($msg2, $uri);

            throw new \Koch\Exception\Exception($msg);
        }
    }

    /**
     * Initialize Doctrine Entity Manager.
     *
     * @param array $config
     * @return \Doctrine\ORM\EntityManager
     * @throws \Exception
     */
    public static function init($config)
    {
        // Database configuration parameters
        self::optionsContainDSN($config);

        // check Doctrine is installed
        /*$em = VENDOR_PATH . '/doctrine/orm/src/EntityManager.php';
         if (is_file($em) === false) {
             throw new \Koch\Exception\Exception('Doctrine not found. Check Vendor Folder.', 100);
         }*/

        // include Doctrine Extensions
        /*$classLoader = new \Doctrine\Common\ClassLoader(
             'doctrine-extensions',
             VENDOR_PATH . 'gedmo/doctrine-extensions/lib/Gedmo'
         );
         $classLoader->register();
         $classLoader = new \Doctrine\Common\ClassLoader(
             'DoctrineExtensions',
             VENDOR_PATH . 'beberlei/DoctrineExtensions/lib'
         );
         $classLoader->register();*/

        // Set up Doctrine configuration
        $paths = [APPLICATION_PATH . 'Doctrine'];
        $isDevMode = defined('DEBUG') ? DEBUG : false;
        $doctrineConfig = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

        // fetch cache driver - APC in production and Array in development mode
        if (extension_loaded('apc') and DEBUG === false) {
            $cache = new \Koch\Cache\Adapter\Apc();
        } else {
            $cache = new \Koch\Cache\Adapter\File();
        }

        // Pass in our own Cache Adapters
        $cache = $isDevMode ? new ArrayAdapter() : new ApcuAdapter();
        $doctrineConfig->setMetadataCache($cache);
        $doctrineConfig->setQueryCache($cache);

        // Set up proxy settings
        $doctrineConfig->setProxyDir(APPLICATION_PATH . 'Doctrine');
        $doctrineConfig->setProxyNamespace('Proxy');
        $doctrineConfig->setAutoGenerateProxyClasses($isDevMode);

        // Set up metadata driver
        $driver = $doctrineConfig->newDefaultAnnotationDriver(self::getModelPathsForAllModules());
        $doctrineConfig->setMetadataDriverImpl($driver);

        // Set up database connection options
        $connectionOptions = [
            'driver'        => $config['database']['driver'],
            'user'          => $config['database']['user'],
            'password'      => $config['database']['password'],
            'dbname'        => $config['database']['dbname'],
            'host'          => $config['database']['host'],
            'charset'       => $config['database']['charset'],
            'driverOptions' => [
                'charset' => $config['database']['charset'],
            ],
        ];

        // Set up event manager and register Gedmo extensions
        $eventManager = new EventManager();
        DoctrineExtensions::registerMappingIntoDriverChainORM($driver, $eventManager);

        // regenerate proxies only in debug and not in production mode
        /*if (DEBUG === true) {
            $D2Config->setAutoGenerateProxyClasses(true);
        } else {
            $D2Config->setAutoGenerateProxyClasses(false);
        }*/

        // set up Logger
        #$config->setSqlLogger(new \Doctrine\DBAL\Logging\EchoSqlLogger);

        // Setup Eventhandling
        $event = new \Doctrine\Common\EventManager();

        /**
         * Set Database Prefix
         *
         * The constant definition is for building (raw) sql queries manually.
         * The database prefixing is registered via an event to Doctrine.
         */
        define('DB_PREFIX', $config['database']['prefix']);
        $tablePrefix = new TablePrefix(DB_PREFIX);
        $eventManager->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);

        /*
         * Register Custom Functions
         *
         * We need some more functions for MySQL, like RAND for random values.
         */
        $doctrineConfig->addCustomNumericFunction('RAND', \Koch\Doctrine\Extensions\Query\Mysql\Rand::class);

        // Finally, create Entity Manager
        $entityManager = EntityManager::create($connectionOptions, $doctrineConfig, $eventManager);

        // Set SQL Logger in debug mode
        // Its also needed for counting queries.
        if ($isDevMode) {
            self::$sqlLoggerStack = new \Doctrine\DBAL\Logging\DebugStack();
            $doctrineConfig->setSQLLogger(self::$sqlLoggerStack);
            // Echo SQL Queries directly on the page.
            $doctrineConfig->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        }

        // set DBAL DebugStack Logger (also needed for counting queries)
        if (defined('DEBUG') and DEBUG === 1) {
            self::$sqlLoggerStack = new \Doctrine\DBAL\Logging\DebugStack();
            $em->getConfiguration()->setSQLLogger(self::$sqlLoggerStack);
            // Echo SQL Queries directly on the page.
            $em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        }

        self::$em = $entityManager;

        // Clean up
        unset($config, $entityManager, $event, $cache, $classLoader, $doctrineConfig);

        return self::$em;
    }

    /**
     * Gets the entity manager to use for all tests.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager()
    {
        return self::$em;
    }

    /**
     * Loads the schema for the given classes.
     *
     * @param array $classes Classes to create the schema for. Defaults to getAllMetadata();
     */
    protected function loadSchema($classes = null)
    {
        $em = static::getEntityManager();

        if ($classes === null) {
            $classes = $em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->createSchema($classes);
    }

    /**
     * Development / Helper Method for Schema Validation.
     */
    public static function validateSchema()
    {
        $em        = self::getEntityManager();
        $validator = new \Doctrine\ORM\Tools\SchemaValidator($em);
        $errors    = $validator->validateMapping();
        \Koch\Debug\Debug::printR($errors);
    }

    /**
     * Development / Helper Method for displaying loaded Models.
     */
    public static function debugLoadedClasses()
    {
        $em     = self::getEntityManager();
        $config = $em->getConfiguration();
        #$config->addEntityNamespace('Core', $module_models_path); // = Core:Session
        #$config->addEntityNamespace('Module', $module_models_path); // = Module:News
        $classes_loaded = $config->getMetadataDriverImpl()->getAllClassNames();
        \Koch\Debug\Debug::printR($classes_loaded);
    }

    /**
     * Fetches Model Paths for all modules.
     *
     * @return array Array with all model directories
     */
    public static function getModelPathsForAllModules()
    {
        $model_dirs = [];

        // get all module directories
        $dirs = glob(APPLICATION_MODULES_PATH . '[a-zA-Z]*', GLOB_ONLYDIR);

        foreach ($dirs as $dir_path) {
            /*
              * It's easier to include dirpath models (subfolder and files will be autoloaded)
              * therefor the records have to be removed
              */
            // Entity Path
            $entity_path = $dir_path . '/Model/Entity';

            if (is_dir($entity_path)) {
                $model_dirs[] = $entity_path;
            }

            // Repository Path
            $repos_path = $dir_path . '/Model/Repository/';

            if (is_dir($repos_path)) {
                $model_dirs[] = $repos_path;
            }
        }

        #$model_dirs[] = APPLICATION_PATH . 'doctrine';

        $model_dirs = array_keys(array_flip($model_dirs));

        return $model_dirs;
    }

    /**
     * Returns Query Counter and the exec time.
     */
    public static function getStats()
    {
        echo sprintf(
            'Doctrine Queries (%d @ %s sec)',
            self::$sqlLoggerStack->currentQuery,
            round(self::getExecTime(), 3)
        );
    }

    /**
     * Returns the Number of Queries.
     *
     * @return int Number of Queries
     */
    public static function getNumberOfQueries()
    {
        return self::$sqlLoggerStack->currentQuery;
    }

    /**
     * Returns the total exec time for queries.
     *
     * @return string Number formatted time string.
     */
    public static function getExecTime()
    {
        $execTime = '';

        foreach (self::$sqlLoggerStack->queries as $query) {
            $execTime += $query['executionMS'];
        }

        return number_format($execTime, 5);
    }
}
