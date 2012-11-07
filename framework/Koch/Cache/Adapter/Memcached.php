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
 * @link http://www.danga.com/memcached/
 * @link http://libmemcached.org/libMemcached.html
 * @link http://php.net/manual/en/book.memcached.php
 *
 * See also the new implementation by Andrei Zmievsk based on libmemcached and memcached.
 * @link http://github.com/andreiz/php-memcached/tree/master
 * @link http://pecl.php.net/package/memcached
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Cache
 */
class Memcached extends AbstractCache implements CacheInterface
{
    /**
     * Memcached Server
     */
    const SERVER_HOST = '127.0.0.1';
    const SERVER_PORT =  11211;
    const SERVER_WEIGHT  = 1;

    /**
     * @var object PHP Memcached instance
     */
    protected $memcached = null;

    /**
     * Constructor.
     *
     * Instantiate and connect to Memcache Server
     */
    public function __construct()
    {
        if (extension_loaded('memcached') === false) {
            throw new Exception(
                'The PHP extension memcache (cache) is not loaded! You may enable it in "php.ini"!'
            );
        }

        // instantiate object und set to class
        $this->memcached = new \Memcached;
        $this->memcached->addServer(self::SERVER_HOST, self::SERVER_PORT, self::SERVER_WEIGTH);

        $this->memcached->setOption(Memcached::OPT_COMPRESSION, true);
        // LIBKETAMA compatibility will implicitly declare the following two things:
        #$this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        #$this->memcached->setOption(Memcached::OPT_HASH, Memcached::MD5);
        $this->memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
    }

    /**
     * Contains checks if a key exists in the cache
     *
     * @param  string  $key Identifier for the data
     * @return boolean true|false
     */
    public function contains($key)
    {
        if ( true === $this->memcached->get($key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Convenience/shortcut method for fetch
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function get($key)
    {
        return $this->fetch($key);
    }

    /**
     * Read a key from the cache
     *
     * @param  string $key Identifier for the data
     * @return mixed  boolean FALSE if the data was not fetched from the cache, DATA on success
     */
    public function fetch($key)
    {
        $result = $this->memcached->get($key);

        if ($result === false) {
            return false;
        } else {
            // typecast $key to array
            if (is_array($result) === false) {
                $result = (array) $result;
            }

            return $result;
        }
    }

    /**
     * Convenience/shortcut method for storing data by key into cache
     *
     * @param  string  $key            Identifier for the data
     * @param  mixed   $data           Data to be cached
     * @param  int $cache_lifetime How long to cache the data, in minutes
     * @return boolean True if the data was successfully cached, false on failure
     */
    public function set($key, $data, $cache_lifetime = 0)
    {
        return $this->store($key, $data, $cache_lifetime * 60);
    }

    /**
     * Stores data by key into cache
     *
     * @param  string  $key            Identifier for the data
     * @param  mixed   $data           Data to be cached
     * @param  int $cache_lifetime How long to cache the data, in minutes
     * @return boolean True if the data was successfully cached, false on failure.
     */
    public function store($key, $data, $cache_lifetime = 0)
    {
        // typecast $data to array
        if (is_array($data) === false) {
            $data = (array) $data;
        }

        if ( $this->memcached->set($key, $data, $cache_lifetime * 60) === true ) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a $key or an array of $keys from the Memcache
     *
     * @param $key string or array
     */
    public function delete($keys)
    {
        // typecast $keys to array
        if (is_array($keys) === false) {
            $keys = (array) $keys;
        }

        foreach ($keys as $key) {
            return $this->memcached->delete($key);
        }
    }

    /**
     * Clears the Cache
     *
     * @return a flushed cache
     */
    public function clear()
    {
        return $this->memcached->flush();
    }

    /**
     * Display Memcached Usage Informations
     */
    public function stats()
    {
        $version    = $this->memcached->getversion();
        $stats      = $this->memcached->getstats();
        $serverlist = $this->memcached->getserverlist();

        // combine arrays
        return compact($version, $stats, $serverlist);
    }

    /**
     * Returns an the Memcached instance
     *
     * @return object \Memcached Cache Engine
     */
    public function getEngine()
    {
        return $this->memcached;
    }

    /**
     * The connection, which was opened using Memcache::connect()
     * will be automatically closed at the end of the script execution.
     * We are nice and close it on object destruction.
     */
    public function __destruct()
    {
        if ($this->memcached !== null) {
            $this->memcached->close();
        }
    }
}
