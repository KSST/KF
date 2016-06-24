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

namespace and path to search in
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine', VENDOR_PATH);
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', VENDOR_PATH . 'Doctrine/Symfony');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Entity', APPLICATION_PATH . 'Doctrine');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Repository', APPLICATION_PATH . 'Doctrine');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Proxy', APPLICATION_PATH . 'Doctrine');
        $classLoader->register();

        // include Doctrine Extensions
        $classLoader = new \Doctrine\Common\ClassLoader(
            'doctrine-extensions',
            VENDOR_PATH . 'gedmo/doctrine-extensions/lib/Gedmo'
        );
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader(
            'DoctrineExtensions',
            VENDOR_PATH . 'beberlei/DoctrineExtensions/lib'
        );
        $classLoader->register();

        // fetch doctrine config handler for configuring
        $D2Config = new \Doctrine\ORM\Configuration();

        // fetch cache driver - APC in production and Array in development mode
        if (extension_loaded('apc') and DEBUG === false) {
            $cache = new \Doctrine\Common\Cache\ApcCache();
        } else {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }

        // set cache driver
        $D2Config->setMetadataCacheImpl($cache);
        $D2Config->setQueryCacheImpl($cache);

        // set annotation driver for entities
        $D2Config->setMetadataDriverImpl($D2Config->newDefaultAnnotationDriver(self::getModelPathsForAllModules()));

        /*
         * This is slow like hell, because getAllClassNames traverses all
         * dirs and files and includes them. Its a workaround, till i find
         * a better way to acquire all the models.
         * @todo optimize this for performance reasons
         */
        $D2Config->getMetadataDriverImpl()->getAllClassNames();
        #\Koch\Debug\Debug::firebug($config->getMetadataDriverImpl()->getAllClassNames());

        // set proxy dirs
        $D2Config->setProxyDir(APPLICATION_PATH . 'Doctrine');
        $D2Config->setProxyNamespace('Proxy');

        // regenerate proxies only in debug and not in production mode
        if (DEBUG === true) {
            $D2Config->setAutoGenerateProxyClasses(true);
        } else {
            $D2Config->setAutoGenerateProxyClasses(false);
        }

        // use main configuration values for setting up the connection
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

        // set up Logger
        #$config->setSqlLogger(new \Doctrine\DBAL\Logging\EchoSqlLogger);

        /*
         * Events
         */
        $event = new \Doctrine\Common\EventManager();

        /*
         * Database Prefix
         *
         * The constant definition is for building (raw) sql queries manually.
         * The database prefixing is registered via an event.
         */
        define('DB_PREFIX', $config['database']['prefix']);

        $tablePrefix = new TablePrefix(DB_PREFIX);
        $event->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);

        /*
         * Custom Functions
         *
         * We need some more functions for MySQL, like RAND for random values.
         */
        $D2Config->addCustomNumericFunction('RAND', 'Koch\Doctrine\Extensions\Query\Mysql\Rand');

        // Entity manager
        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $D2Config, $event);

        // set DBAL DebugStack Logger (also needed for counting queries)
        if (defined('DEBUG') and DEBUG === 1) {
            self::$sqlLoggerStack = new \Doctrine\DBAL\Logging\DebugStack();
            $em->getConfiguration()->setSQLLogger(self::$sqlLoggerStack);
            // Echo SQL Queries directly on the page.
            $em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        }

        self::$em = $em;

        // the D2 initalization is done, remove vars to safe memory
        unset($config, $em, $event, $cache, $classLoader, $D2Config);

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
        $em = $this->getEntityManager();

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

        foreach ($dirs as $key => $dir_path) {
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
