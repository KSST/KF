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
 *
 */

namespace Koch\Doctrine;

/**
 * Koch Framework - Class for the Initialization of Doctrine 2.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Doctrine
 */
class Doctrine
{
    /**
     * A DBAL Logger Object
     *
     * @var object \Doctrine\DBAL\Logging\DebugStack
     */
    private static $sqlLoggerStack = '';

    /**
     * Doctrine2 Entity Manager
     *
     * @var object \Doctrine\ORM\EntityManager
     */
    private static $em;

    /**
     * Total execution time for all queries
     *
     * @var float
     */
    private static $execTime = '';

    /**
     * Ensure that Database Connection Informations are present
     * in configuration. If not, point back to the installation.
     */
    public static function checkDataSourceName($config)
    {
        // check if database settings are available
        if (empty($config['database']['driver']) === true
        or empty($config['database']['user']) === true
        or empty($config['database']['host']) === true
        or empty($config['database']['dbname']) === true) {
            $msg1 = _('The database connection configuration is missing.');
            $msg2 = _('Please use <a href=%s>Installation</a> to perform a proper installation.');

            $uri = sprintf('http://%s%s', $_SERVER['SERVER_NAME'], '/installation/index.php');

            $msg = $msg1 . NL . sprintf($msg2, $uri);

            throw new \Koch\Exception\Exception($msg);
        }
    }

    /**
     * Initialize auto loader of Doctrine
     *
     * @return Doctrine_Enitity_Manager
     */
    public static function init($applicationConfig)
    {
        self::checkDataSourceName($applicationConfig);

        $vendor = VENDOR_PATH . '/doctrine/common/lib/';

        // ensure doctrine2 exists in the libraries folder
        if (is_file($vendor . 'Doctrine/Common/ClassLoader.php') === false) {
            throw new \Koch\Exception\Exception('Doctrine2 not found. Check Libraries Folder.', 100);
        }

        // get isolated loader
        require $vendor . 'Doctrine/Common/ClassLoader.php';

        // setup autoloaders with namespace and path to search in
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine', VENDOR_PATH);
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Symfony', VENDOR_PATH .  'Doctrine/Symfony');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Entity', APPLICATION_PATH . 'Doctrine');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Repository', APPLICATION_PATH . 'Doctrine');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Proxy', APPLICATION_PATH . 'Doctrine');
        $classLoader->register();

        // include Doctrine Extensions
        $classLoader = new \Doctrine\Common\ClassLoader('DoctrineExtensions', VENDOR_PATH . 'gedmo/doctrine-extensions/lib/Gedmo');
        $classLoader->register();

        // fetch doctrine config handler for configuring
        $config = new \Doctrine\ORM\Configuration();

        // fetch cache driver - APC in production and Array in development mode
        if (extension_loaded('apc') and DEBUG == false) {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        }

        // set cache driver
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // set annotation driver for entities
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(self::getModelPathsForAllModules()));

        /**
         * This is slow like hell, because getAllClassNames traverses all
         * dirs and files and includes them. Its a workaround, till i find
         * a better way to acquire all the models.
         * @todo optimize this for performance reasons
         */
        $config->getMetadataDriverImpl()->getAllClassNames();
        #\Koch\Debug\Debug::firebug($config->getMetadataDriverImpl()->getAllClassNames());

        // set proxy dirs
        $config->setProxyDir(APPLICATION_PATH . 'Doctrine');
        $config->setProxyNamespace('Proxy');

        // regenerate proxies only in debug and not in production mode
        if (DEBUG == true) {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        // use main configuration values for setting up the connection
        $connectionOptions = array(
            'driver'    => $applicationConfig['database']['driver'],
            'user'      => $applicationConfig['database']['user'],
            'password'  => $applicationConfig['database']['password'],
            'dbname'    => $applicationConfig['database']['dbname'],
            'host'      => $applicationConfig['database']['host'],
            'charset'   => $applicationConfig['database']['charset'],
            'driverOptions' => array(
                'charset' => $applicationConfig['database']['charset']
            )
        );

        // set up Logger
        #$config->setSqlLogger(new \Doctrine\DBAL\Logging\EchoSqlLogger);

        // we need some more functions for mysql
        $config->addCustomNumericFunction('RAND', 'DoctrineExtensions\Query\Mysql\Rand');

        /**
         * Events
         */
        $event = new \Doctrine\Common\EventManager;

        /**
         * Database Prefix
         *
         * The constant definition is for building (raw) sql queries manually.
         * The database prefixing is registered via an event.
         */
        define('DB_PREFIX', $applicationConfig['database']['prefix']);

        $tablePrefix = new TablePrefix(DB_PREFIX);
        $event->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);

        /**
         * Custom Functions
         *
         * We need some more functions for mysql, like RAND for random values.
         */
        $config->addCustomNumericFunction('RAND', 'DoctrineExtensions\Query\Mysql\Rand');

        /**
         * Set UTF-8 handling of database data via Doctrine Event for MySQL.
         */
        if ($applicationConfig['database']['driver'] !== null
        and $applicationConfig['database']['driver'] == "pdo_mysql") {
            if ($applicationConfig['database']['charset'] !== null) {
                $event->addEventSubscriber(
                    new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit(
                        $applicationConfig['database']['charset'],
                        'utf8_unicode_ci'
                    )
                );
            }
        }

        // Entity manager
        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config, $event);

        // set DBAL DebugStack Logger (also needed for counting queries)
        if (defined('DEBUG') and DEBUG == 1) {
            self::$sqlLoggerStack = new \Doctrine\DBAL\Logging\DebugStack();
            $em->getConfiguration()->setSQLLogger(self::$sqlLoggerStack);

            /**
             * Echo SQL Queries directly on the page.
             */
            #$em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        }

        self::$em = $em;

        // done with config, remove to safe memory
        unset($applicationConfig, $em, $event, $cache, $classLoader, $config);

        return self::$em;
    }

    /**
     * Gets the entity manager to use for all tests
     *
     * @return Doctrine\ORM\EntityManager
     */
    public static function getEntityManager()
    {
        return self::$em;
    }

    /**
     * Loads the schema for the given classes
     *
     * @param array $classes Classes to create the schema for. Defaults to getAllMetadata();
     */
    protected function loadSchema($classes = null)
    {
        if ($classes === null) {
            $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->getEntityManager());
        $schemaTool->createSchema($classes);
    }

    /**
     * Development / Helper Method for Schema Validation
     */
    public static function validateSchema()
    {
        $em = self::getEntityManager();
        $validator = new \Doctrine\ORM\Tools\SchemaValidator($em);
        $errors = $validator->validateMapping();
        \Koch\Debug\Debug::printR($errors);
    }

    /**
     * Development / Helper Method for displaying loaded Models
     */
    public static function debugLoadedClasses()
    {
        $em = self::getEntityManager();
        $config = $em->getConfiguration();
        #$config->addEntityNamespace('Core', $module_models_path); // = Core:Session
        #$config->addEntityNamespace('Module', $module_models_path); // = Module:News
        $classes_loaded = $config->getMetadataDriverImpl()->getAllClassNames();
        \Koch\Debug\Debug::printR($classes_loaded);
    }

    /**
     * Fetches Model Paths for all modules
     *
     * @return array Array with all model directories
     */
    public static function getModelPathsForAllModules()
    {
        $model_dirs = array();

        // get all module directories
        $dirs = glob(APPLICATION_MODULES_PATH . '[a-zA-Z]*', GLOB_ONLYDIR);

        foreach ($dirs as $key => $dir_path) {
            /**
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

        #\Koch\Debug\Debug::printR($model_dirs);

        return $model_dirs;
    }

    /**
     * Returns Query Counter and the exec time
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
     * Returns the Number of Queries
     *
     * @return int Number of Queries
     */
    public static function getNumberOfQueries()
    {
        return self::$sqlLoggerStack->currentQuery;
    }

    /**
     * Returns the total exec time for queries
     *
     * @return string Number formatted time string.
     */
    public static function getExecTime()
    {
        $execTime = '';

        foreach (self::$sqlLoggerStack->queries as $query) {
            $execTime += $query['executionMS'];
        }

        $execTime = number_format($execTime, 5);

        return $execTime;
    }

    /**
     * var_dumps the logger stack
     * for a simple overview of all queries
     */
    public static function getLoggerStack()
    {
        // @todo debug dump to firebug?
        var_dump(self::$sqlLoggerStack);
    }
}
