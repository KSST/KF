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

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;
use Koch\Exception\Exception;

/**
 * Cache Handler for Memcached.
 *
 * memcached is a high-performance, distributed memory object caching system, generic in nature,
 * but intended for use in speeding up dynamic web applications by alleviating database load.
 * memcached was developed by Danga Interactive to enhance the speed of LiveJournal.com.
 * You need two things to get this running: a memcache daemon (server) and the php extension memcached.
 *
 * More information can be obtained here:
 *
 * @link http://www.danga.com/memcached/
 * @link http://libmemcached.org/libMemcached.html
 * @link http://php.net/manual/en/book.memcached.php
 *
 * See also the new implementation by Andrei Zmievski based on libmemcached and memcached.
 * @link http://github.com/andreiz/php-memcached/tree/master
 * @link http://pecl.php.net/package/memcached
 */
class Memcached extends AbstractCache implements CacheInterface
{
    /**
     * @var object PHP Memcached instance
     */
    protected $memcached;

    /**
     * Constructor.
     *
     * Instantiate and connect to Memcache Server
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (extension_loaded('memcached') === false) {
            throw new Exception(
                'The PHP extension memcache (cache) is not loaded! You may enable it in "php.ini"!'
            );
        }

        $defaultOptions = [
            'useConnection' => 'default',
            'connection'    => [
                'default' => [
                    'servers' => [
                        ['host' => '127.0.0.1', 'port' => 11211, 'persistent' => true],
                    ], ], ],
        ];

        $options += $defaultOptions;

        parent::__construct($options);

        $this->memcached = $this->getMemcachedInstance($options['useConnection']);
    }

    /**
     * Sets a single option.
     *
     * @param string Key.
     * @param mixed Value.
     *
     * @return bool True, if successfull.
     */
    public function setOption($key, $value)
    {
        switch ($key) {
            case 'useConnection':
                $value = (string) $value;
                if ($value === '') {
                    throw new \InvalidArgumentException('useConnection options can not be empty.');
                }
                $this->options['useConnection'] = $value;
                break;
            case 'connection':
                $connection = (array) $value;
                if (is_array($connection) === false) {
                    throw new \InvalidArgumentException('Connection option must be array.');
                }
                $this->options['connection'] = $connection;
                break;
            default:
                // maybe the option is known on the parent class, otherwise will throw excetion
                parent::setOption($key, $value);
        }

        return true;
    }

    public function getMemcachedInstance($connection = 'default')
    {
        // one instantiation (per-connection per-request)
        static $instances = [];

        // return early, if connection already exists
        if (array_key_exists($connection, $instances)) {
            return $instances[$connection];
        }

        // instantiate new connection
        $memcached = new \Memcached($connection);

        $memcached->setOption(\Memcached::OPT_PREFIX_KEY, $this->options['prefix']);
        $memcached->setOption(\Memcached::OPT_COMPRESSION, true);
        $memcached->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        // Note: setting LIBKETAMA compatibility will implicitly declare the following two things:
        #$this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        #$this->memcached->setOption(Memcached::OPT_HASH, Memcached::MD5);

        if (count($memcached->getServerList()) === 0) {
            if (isset($this->options[$connection]) || array_key_exists($connection, $this->options['connection'])) {
                // specific servers set per connection
                $memcached->addServers($this->options['connection'][$connection]['servers']);
            } else {
                // default servers
                $memcached->addServers($this->options['connection']['default']['servers']);
            }
        }

        // add instance to the pool
        $instances[$connection] = $memcached;

        return $memcached;
    }

    /**
     * Contains checks if a key exists in the cache.
     *
     * @param string $key Identifier for the data
     *
     * @return bool true|false
     */
    public function contains($key)
    {
        return (true === (bool) $this->memcached->get($key)) ? true : false;
    }

    /**
     * Read a key from the cache.
     *
     * @param string $key Identifier for the data
     *
     * @return mixed bool FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        $result = $this->memcached->get($key);

        return ($result === false) ? false : $result;
    }

    /**
     * Stores data by key into cache.
     *
     * @param string $key  Identifier for the data
     * @param string $data Data to be cached
     * @param int    $ttl  How long to cache the data (in minutes).
     *
     * @return bool True if the data was successfully cached, false on failure.
     */
    public function store($key, $data, $ttl = null)
    {
        if (null === $ttl) {
            $ttl = $this->options['ttl'];
        }

        return $this->memcached->set($key, $data, time() + $ttl);
    }

    /**
     * Deletes a $key or an array of $keys from the Memcache.
     *
     * @param $key string
     */
    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * Clears the Cache.
     *
     * @return bool flushed cache
     */
    public function clear()
    {
        return $this->memcached->flush();
    }

    /**
     * Display Memcached Usage Informations.
     */
    public function stats()
    {
        $version    = $this->memcached->getVersion();
        $stats      = $this->memcached->getStats();
        $serverlist = $this->memcached->getServerList();

        // combine arrays
        return compact($version, $stats, $serverlist);
    }

    /**
     * Returns an the Memcached instance.
     *
     * @return object \Memcached Cache Engine
     */
    public function getEngine()
    {
        return $this->memcached;
    }
}
